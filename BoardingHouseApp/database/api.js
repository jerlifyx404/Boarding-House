const http = require('http');
const url = require('url');
const mysql = require('mysql2');
const CryptoJS = require('crypto-js');
const fs = require('fs');
const path = require('path');
const multer = require('multer');

// MySQL Database Connection
const db = mysql.createConnection({
  host: 'localhost',
  user: 'root',
  password: '',
  database: 'BoardingHouse',
});

db.connect(err => {
  if (err) {
    console.error('Error connecting to MySQL:', err);
    process.exit(1);
  }
  console.log('Connected to MySQL database');
});

// Configure Multer for file uploads
const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    const uploadDir = path.join(__dirname, 'Uploads');
    if (!fs.existsSync(uploadDir)) {
      fs.mkdirSync(uploadDir, { recursive: true });
    }
    cb(null, uploadDir);
  },
  filename: (req, file, cb) => {
    const uniqueSuffix = `${Date.now()}-${Math.round(Math.random() * 1e9)}`;
    cb(null, `${uniqueSuffix}${path.extname(file.originalname)}`);
  },
});

const upload = multer({
  storage,
  limits: { fileSize: 5 * 1024 * 1024 }, // 5MB limit
  fileFilter: (req, file, cb) => {
    const filetypes = /jpeg|jpg|png/;
    const extname = filetypes.test(path.extname(file.originalname).toLowerCase());
    const mimetype = filetypes.test(file.mimetype);
    if (extname && mimetype) {
      cb(null, true);
    } else {
      cb(new Error('Only JPEG and PNG images are allowed'));
    }
  },
});

// HTTP Server
const BASE_URL = 'http://192.168.165.222:8080';
const server = http.createServer((req, res) => {
  const parsedUrl = url.parse(req.url, true);
  const method = req.method;
  const pathname = parsedUrl.pathname;

  // Handle CORS
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

  if (method === 'OPTIONS') {
    res.statusCode = 204;
    res.end();
    return;
  }

  // Serve static image files
  if (pathname.startsWith('/Uploads/')) {
    const filePath = path.join(__dirname, pathname);
    fs.readFile(filePath, (err, data) => {
      if (err) {
        console.error(`Image not found: ${filePath}`, err);
        res.statusCode = 404;
        res.setHeader('Content-Type', 'text/plain');
        res.end('Image not found');
        return;
      }
      const ext = path.extname(filePath).toLowerCase();
      const contentType =
        {
          '.jpg': 'image/jpeg',
          '.jpeg': 'image/jpeg',
          '.png': 'image/png',
        }[ext] || 'application/octet-stream';
      res.statusCode = 200;
      res.setHeader('Content-Type', contentType);
      res.end(data);
    });
    return;
  }

  // Set default content type for API responses
  res.setHeader('Content-Type', 'application/json');

  // Handle file uploads for /boarding endpoints
  if ((method === 'POST' || method === 'PUT') && pathname.match(/^\/boarding(\/\d+)?$/)) {
    const houseID = pathname.split('/')[2];
    upload.array('photos', 10)(req, res, err => {
      if (err) {
        console.error('Multer error:', err);
        res.statusCode = 400;
        res.end(JSON.stringify({ error: err.message }));
        return;
      }

      const { ownerID, name, address, NumberOfRooms, pNum, price, photosToDelete } = req.body;
      if (!name || !address || !NumberOfRooms || !pNum || !price || (method === 'POST' && !ownerID)) {
        res.statusCode = 400;
        res.end(JSON.stringify({ error: 'All fields are required' }));
        return;
      }

      let photosToDeleteArray = [];
      if (photosToDelete) {
        try {
          photosToDeleteArray = JSON.parse(photosToDelete);
        } catch (e) {
          console.error('Error parsing photosToDelete:', e);
        }
      }

      const query =
        method === 'POST'
          ? 'INSERT INTO BoardingDetails (ownerID, name, address, NumberOfRooms, pNum, price) VALUES (?, ?, ?, ?, ?, ?)'
          : 'UPDATE BoardingDetails SET name = ?, address = ?, NumberOfRooms = ?, pNum = ?, price = ? WHERE houseID = ?';
      const queryParams =
        method === 'POST'
          ? [ownerID, name, address, NumberOfRooms, pNum, price]
          : [name, address, NumberOfRooms, pNum, price, houseID];

      db.query(query, queryParams, (err, results) => {
        if (err) {
          console.error(`Error ${method === 'POST' ? 'creating' : 'updating'} boarding house:`, err);
          res.statusCode = 500;
          res.end(JSON.stringify({ error: `Error ${method === 'POST' ? 'creating' : 'updating'} boarding house` }));
          return;
        }

        if (method === 'PUT' && results.affectedRows === 0) {
          res.statusCode = 404;
          res.end(JSON.stringify({ error: 'Boarding house not found' }));
          return;
        }

        const newHouseID = method === 'POST' ? results.insertId : houseID;

        // Handle photo deletions
        if (method === 'PUT' && photosToDeleteArray.length > 0) {
          const deleteQuery = 'DELETE FROM Photos WHERE houseID = ? AND photoUrl IN (?)';
          db.query(deleteQuery, [houseID, photosToDeleteArray], deleteErr => {
            if (deleteErr) {
              console.error('Error deleting photos:', deleteErr);
            }
            photosToDeleteArray.forEach(photoUrl => {
              const filePath = path.join(__dirname, photoUrl);
              fs.unlink(filePath, err => {
                if (err) console.error('Error deleting file:', err);
              });
            });
          });
        }

        // Add new photos
        const newPhotos = req.files.map(file => `/Uploads/${file.filename}`);
        if (newPhotos.length > 0) {
          const photoQuery = 'INSERT INTO Photos (houseID, photoUrl) VALUES ?';
          const photoValues = newPhotos.map(photo => [newHouseID, photo]);
          db.query(photoQuery, [photoValues], photoErr => {
            if (photoErr) {
              console.error('Error saving new photos:', photoErr);
            }
          });
        }

        res.statusCode = method === 'POST' ? 201 : 200;
        res.end(
          JSON.stringify({
            message: `Boarding house ${method === 'POST' ? 'created' : 'updated'} successfully`,
            houseID: newHouseID,
          })
        );
      });
    });
    return;
  }

  // Collect request body for non-file endpoints
  let body = '';
  req.on('data', chunk => {
    body += chunk.toString();
  });

  req.on('end', () => {
    let data;
    try {
      data = body ? JSON.parse(body) : {};
    } catch (e) {
      res.statusCode = 400;
      res.end(JSON.stringify({ error: 'Invalid JSON' }));
      return;
    }

    // Signup Endpoint
    if (method === 'POST' && pathname === '/signup') {
      const { fullName, username, email, userType, password } = data;
      if (!fullName || !username || !email || !userType || !password) {
        res.statusCode = 400;
        res.end(JSON.stringify({ error: 'All fields are required' }));
        return;
      }
      if (!['Owner', 'Tenant'].includes(userType)) {
        res.statusCode = 400;
        res.end(JSON.stringify({ error: 'userType must be Owner or Tenant' }));
        return;
      }

      const hashedPassword = CryptoJS.SHA256(password).toString();
      const query = 'INSERT INTO Users (fullName, username, email, userType, password) VALUES (?, ?, ?, ?, ?)';
      db.query(query, [fullName, username, email, userType, hashedPassword], (err, results) => {
        if (err) {
          console.error('Error creating user:', err);
          res.statusCode = 500;
          res.end(JSON.stringify({ error: 'Error creating user' }));
          return;
        }
        res.statusCode = 201;
        res.end(JSON.stringify({ message: 'User created successfully', userID: results.insertId }));
      });
    }
    // Login Endpoint
    else if (method === 'POST' && pathname === '/login') {
      const { username, password } = data;
      if (!username || !password) {
        res.statusCode = 400;
        res.end(JSON.stringify({ error: 'Username and password are required' }));
        return;
      }

      const hashedPassword = CryptoJS.SHA256(password).toString();
      const query = 'SELECT * FROM Users WHERE username = ? AND password = ?';
      db.query(query, [username, hashedPassword], (err, results) => {
        if (err) {
          console.error('Error during login:', err);
          res.statusCode = 500;
          res.end(JSON.stringify({ error: 'Error during login' }));
          return;
        }
        if (results.length === 0) {
          res.statusCode = 401;
          res.end(JSON.stringify({ error: 'Invalid credentials' }));
          return;
        }
        const user = results[0];
        res.statusCode = 200;
        res.end(
          JSON.stringify({
            message: 'Login successful',
            user: {
              userID: user.userID,
              fullName: user.fullName,
              username: user.username,
              email: user.email,
              userType: user.userType,
            },
          })
        );
      });
    }
    // Get All Users Endpoint
    else if (method === 'GET' && pathname === '/users') {
      const query = 'SELECT userID, fullName, username, email, userType FROM Users';
      db.query(query, (err, results) => {
        if (err) {
          console.error('Error retrieving users:', err);
          res.statusCode = 500;
          res.end(JSON.stringify({ error: 'Error retrieving users' }));
          return;
        }
        res.statusCode = 200;
        res.end(JSON.stringify({ users: results }));
      });
    }
    // Get User by ID Endpoint
    else if (method === 'GET' && pathname.match(/^\/users\/\d+$/)) {
      const userID = pathname.split('/')[2];
      const query = 'SELECT userID, fullName, username, email, userType FROM Users WHERE userID = ?';
      db.query(query, [userID], (err, results) => {
        if (err) {
          console.error('Error retrieving user:', err);
          res.statusCode = 500;
          res.end(JSON.stringify({ error: 'Error retrieving user' }));
          return;
        }
        if (results.length === 0) {
          res.statusCode = 404;
          res.end(JSON.stringify({ error: 'User not found' }));
          return;
        }
        res.statusCode = 200;
        res.end(JSON.stringify({ user: results[0] }));
      });
    }
    // Update User Endpoint
    else if (method === 'PUT' && pathname.match(/^\/users\/\d+$/)) {
      const userID = pathname.split('/')[2];
      const { fullName, username, email, userType, password } = data;
      if (!fullName || !username || !email || !userType) {
        res.statusCode = 400;
        res.end(JSON.stringify({ error: 'fullName, username, email, and userType are required' }));
        return;
      }
      if (!['Owner', 'Tenant'].includes(userType)) {
        res.statusCode = 400;
        res.end(JSON.stringify({ error: 'userType must be Owner or Tenant' }));
        return;
      }

      const updates = { fullName, username, email, userType };
      if (password) {
        updates.password = CryptoJS.SHA256(password).toString();
      }
      const query = 'UPDATE Users SET ? WHERE userID = ?';
      db.query(query, [updates, userID], (err, results) => {
        if (err) {
          console.error('Error updating user:', err);
          res.statusCode = 500;
          res.end(JSON.stringify({ error: 'Error updating user' }));
          return;
        }
        if (results.affectedRows === 0) {
          res.statusCode = 404;
          res.end(JSON.stringify({ error: 'User not found' }));
          return;
        }
        res.statusCode = 200;
        res.end(JSON.stringify({ message: 'User updated successfully' }));
      });
    }
    // Delete User Endpoint
    else if (method === 'DELETE' && pathname.match(/^\/users\/\d+$/)) {
      const userID = pathname.split('/')[2];
      const query = 'DELETE FROM Users WHERE userID = ?';
      db.query(query, [userID], (err, results) => {
        if (err) {
          console.error('Error deleting user:', err);
          res.statusCode = 500;
          res.end(JSON.stringify({ error: 'Error deleting user' }));
          return;
        }
        if (results.affectedRows === 0) {
          res.statusCode = 404;
          res.end(JSON.stringify({ error: 'User not found' }));
          return;
        }
        res.statusCode = 200;
        res.end(JSON.stringify({ message: 'User deleted successfully' }));
      });
    }
    // Get All Boarding Houses Endpoint
    else if (method === 'GET' && pathname === '/boarding/all') {
      const query = `
        SELECT bd.*, u.fullName AS ownerName, GROUP_CONCAT(p.photoUrl) as photos
        FROM BoardingDetails bd
        LEFT JOIN Users u ON bd.ownerID = u.userID
        LEFT JOIN Photos p ON bd.houseID = p.houseID
        GROUP BY bd.houseID
      `;
      db.query(query, (err, results) => {
        if (err) {
          console.error('Error retrieving all boarding houses:', err);
          res.statusCode = 500;
          res.end(JSON.stringify({ error: 'Error retrieving boarding houses' }));
          return;
        }
        const boardingHouses = results.map(house => ({
          ...house,
          price: house.price !== null ? parseFloat(house.price) : 0,
          ownerName: house.ownerName || 'Unknown Owner',
          photos: house.photos
            ? house.photos.split(',').map(url => {
                if (url.startsWith('http')) {
                  return url;
                }
                return url.startsWith('/Uploads/') ? `${BASE_URL}${url}` : `${BASE_URL}/Uploads/${url}`;
              })
            : [],
        }));
        res.statusCode = 200;
        res.end(JSON.stringify({ boardingHouses }));
      });
    }
    // Get Boarding Houses by Owner Endpoint
    else if (method === 'GET' && pathname === '/boarding/owner') {
      const { ownerID } = parsedUrl.query;
      if (!ownerID) {
        res.statusCode = 400;
        res.end(JSON.stringify({ error: 'ownerID is required' }));
        return;
      }

      const query = `
        SELECT bd.*, u.fullName AS ownerName, GROUP_CONCAT(p.photoUrl) as photos
        FROM BoardingDetails bd
        LEFT JOIN Users u ON bd.ownerID = u.userID
        LEFT JOIN Photos p ON bd.houseID = p.houseID
        WHERE bd.ownerID = ?
        GROUP BY bd.houseID
      `;
      db.query(query, [ownerID], (err, results) => {
        if (err) {
          console.error('Error retrieving boarding houses:', err);
          res.statusCode = 500;
          res.end(JSON.stringify({ error: 'Error retrieving boarding houses' }));
          return;
        }
        const boardingHouses = results.map(house => ({
          ...house,
          price: house.price !== null ? parseFloat(house.price) : 0,
          ownerName: house.ownerName || 'Unknown Owner',
          photos: house.photos
            ? house.photos.split(',').map(url => {
                if (url.startsWith('http')) {
                  return url;
                }
                return url.startsWith('/Uploads/') ? `${BASE_URL}${url}` : `${BASE_URL}/Uploads/${url}`;
              })
            : [],
        }));
        res.statusCode = 200;
        res.end(JSON.stringify({ boardingHouses }));
      });
    }
    // Get Boarding House by ID Endpoint
    else if (method === 'GET' && pathname.match(/^\/boarding\/\d+$/)) {
      const houseID = pathname.split('/')[2];
      const query = `
        SELECT bd.*, u.fullName AS ownerName, GROUP_CONCAT(p.photoUrl) as photos
        FROM BoardingDetails bd
        LEFT JOIN Users u ON bd.ownerID = u.userID
        LEFT JOIN Photos p ON bd.houseID = p.houseID
        WHERE bd.houseID = ?
        GROUP BY bd.houseID
      `;
      db.query(query, [houseID], (err, results) => {
        if (err) {
          console.error('Error retrieving boarding house:', err);
          res.statusCode = 500;
          res.end(JSON.stringify({ error: 'Error retrieving boarding house' }));
          return;
        }
        if (results.length === 0) {
          res.statusCode = 404;
          res.end(JSON.stringify({ error: 'Boarding house not found' }));
          return;
        }
        const house = {
          ...results[0],
          price: results[0].price !== null ? parseFloat(results[0].price) : 0,
          ownerName: results[0].ownerName || 'Unknown Owner',
          photos: results[0].photos
            ? results[0].photos.split(',').map(url => {
                if (url.startsWith('http')) {
                  return url;
                }
                return url.startsWith('/Uploads/') ? `${BASE_URL}${url}` : `${BASE_URL}/Uploads/${url}`;
              })
            : [],
        };
        res.statusCode = 200;
        res.end(JSON.stringify({ boardingHouse: house }));
      });
    }
    // Delete Boarding House Endpoint
    else if (method === 'DELETE' && pathname.match(/^\/boarding\/\d+$/)) {
      const houseID = pathname.split('/')[2];
      db.query('SELECT photoUrl FROM Photos WHERE houseID = ?', [houseID], (photoErr, photos) => {
        if (photoErr) {
          console.error('Error fetching photos for deletion:', photoErr);
        }

        db.query('DELETE FROM Photos WHERE houseID = ?', [houseID], photoDeleteErr => {
          if (photoDeleteErr) {
            console.error('Error deleting photos:', photoDeleteErr);
          }

          db.query('DELETE FROM BoardingDetails WHERE houseID = ?', [houseID], (err, results) => {
            if (err) {
              console.error('Error deleting boarding house:', err);
              res.statusCode = 500;
              res.end(JSON.stringify({ error: 'Error deleting boarding house' }));
              return;
            }
            if (results.affectedRows === 0) {
              res.statusCode = 404;
              res.end(JSON.stringify({ error: 'Boarding house not found' }));
              return;
            }

            if (photos && photos.length > 0) {
              photos.forEach(photo => {
                const filePath = path.join(__dirname, photo.photoUrl);
                fs.unlink(filePath, err => {
                  if (err) console.error('Error deleting file:', err);
                });
              });
            }

            res.statusCode = 200;
            res.end(JSON.stringify({ message: 'Boarding house deleted successfully' }));
          });
        });
      });
    }
    // Create Rental Request Endpoint
    else if (method === 'POST' && pathname === '/rental-requests') {
      const { tenantID, houseID, fullName, mobileNumber, email, roomPreference } = data;
      if (!tenantID || !houseID || !fullName || !mobileNumber || !email || !roomPreference) {
        res.statusCode = 400;
        res.end(JSON.stringify({ error: 'All fields are required' }));
        return;
      }
      if (!['Single Room', 'Shared Room'].includes(roomPreference)) {
        res.statusCode = 400;
        res.end(JSON.stringify({ error: 'roomPreference must be Single Room or Shared Room' }));
        return;
      }

      const query = 'INSERT INTO RentalRequests (tenantID, houseID, fullName, mobileNumber, email, roomPreference) VALUES (?, ?, ?, ?, ?, ?)';
      db.query(query, [tenantID, houseID, fullName, mobileNumber, email, roomPreference], (err, results) => {
        if (err) {
          console.error('Error creating rental request:', err);
          res.statusCode = 500;
          res.end(JSON.stringify({ error: 'Error creating rental request' }));
          return;
        }

        // Create notification for the owner
        const ownerQuery = 'SELECT ownerID FROM BoardingDetails WHERE houseID = ?';
        db.query(ownerQuery, [houseID], (ownerErr, ownerResults) => {
          if (ownerErr || ownerResults.length === 0) {
            console.error('Error fetching owner or owner not found:', ownerErr);
          } else {
            const ownerID = ownerResults[0].ownerID;
            const notificationQuery = 'INSERT INTO Notifications (requestID, userID, houseID, status, `read`) VALUES (?, ?, ?, ?, ?)';
            db.query(notificationQuery, [results.insertId, ownerID, houseID, 'pending', false], (notifErr) => {
              if (notifErr) {
                console.error('Error creating notification:', notifErr);
              }
            });
          }
        });

        res.statusCode = 201;
        res.end(JSON.stringify({ message: 'Rental request created successfully', requestID: results.insertId }));
      });
    }
    // Get Rental Requests by Tenant or House Endpoint
    else if (method === 'GET' && pathname === '/rental-requests') {
      const { tenantID, houseID } = parsedUrl.query;
      if (!tenantID && !houseID) {
        res.statusCode = 400;
        res.end(JSON.stringify({ error: 'tenantID or houseID is required' }));
        return;
      }

      let query = 'SELECT * FROM RentalRequests WHERE ';
      let queryParams = [];
      if (tenantID) {
        query += 'tenantID = ?';
        queryParams.push(tenantID);
      } else if (houseID) {
        query += 'houseID = ?';
        queryParams.push(houseID);
      }

      db.query(query, queryParams, (err, results) => {
        if (err) {
          console.error('Error retrieving rental requests:', err);
          res.statusCode = 500;
          res.end(JSON.stringify({ error: 'Error retrieving rental requests' }));
          return;
        }
        res.statusCode = 200;
        res.end(JSON.stringify({ rentalRequests: results }));
      });
    }
    // Get Rental Requests by Owner Endpoint
    else if (method === 'GET' && pathname === '/rental-requests/owner') {
      const { ownerID } = parsedUrl.query;
      if (!ownerID) {
        res.statusCode = 400;
        res.end(JSON.stringify({ error: 'ownerID is required' }));
        return;
      }

      const query = `
        SELECT rr.*, bd.name AS houseName
        FROM RentalRequests rr
        INNER JOIN BoardingDetails bd ON rr.houseID = bd.houseID
        WHERE bd.ownerID = ? AND rr.status = 'pending'
      `;
      db.query(query, [ownerID], (err, results) => {
        if (err) {
          console.error('Error retrieving rental requests for owner:', err);
          res.statusCode = 500;
          res.end(JSON.stringify({ error: 'Error retrieving rental requests' }));
          return;
        }
        res.statusCode = 200;
        res.end(JSON.stringify({ rentalRequests: results }));
      });
    }
    // Update Rental Request Status Endpoint
    else if (method === 'PUT' && pathname.match(/^\/rental-requests\/\d+$/)) {
      const requestID = pathname.split('/')[2];
      const { status } = data;
      if (!status) {
        res.statusCode = 400;
        res.end(JSON.stringify({ error: 'status is required' }));
        return;
      }
      if (!['pending', 'approved', 'declined'].includes(status)) {
        res.statusCode = 400;
        res.end(JSON.stringify({ error: 'status must be pending, approved, or declined' }));
        return;
      }

      // Update RentalRequests table
      const query = 'UPDATE RentalRequests SET status = ? WHERE requestID = ?';
      db.query(query, [status, requestID], (err, results) => {
        if (err) {
          console.error('Error updating rental request:', err);
          res.statusCode = 500;
          res.end(JSON.stringify({ error: 'Error updating rental request' }));
          return;
        }
        if (results.affectedRows === 0) {
          res.statusCode = 404;
          res.end(JSON.stringify({ error: 'Rental request not found' }));
          return;
        }

        // Fetch houseID, tenantID, and ownerID
        db.query(`
          SELECT rr.houseID, rr.tenantID, bd.ownerID
          FROM RentalRequests rr
          INNER JOIN BoardingDetails bd ON rr.houseID = bd.houseID
          WHERE rr.requestID = ?
        `, [requestID], (selectErr, selectResults) => {
          if (selectErr || selectResults.length === 0) {
            console.error('Error fetching rental request details:', selectErr);
            res.statusCode = 500;
            res.end(JSON.stringify({ error: 'Error processing notifications' }));
            return;
          }

          const { houseID, tenantID, ownerID } = selectResults[0];

          // Update owner's notification: set read = true and status
          db.query(
            'UPDATE Notifications SET status = ?, `read` = ? WHERE requestID = ? AND userID = ?',
            [status, true, requestID, ownerID],
            (updateErr) => {
              if (updateErr) {
                console.error('Error updating owner notification:', updateErr);
              }

              // Create tenant notification
              db.query(
                'INSERT INTO Notifications (requestID, userID, houseID, status, `read`) VALUES (?, ?, ?, ?, ?)',
                [requestID, tenantID, houseID, status, false],
                (notifErr) => {
                  if (notifErr) {
                    console.error('Error creating tenant notification:', notifErr);
                  }
                }
              );

              res.statusCode = 200;
              res.end(JSON.stringify({ message: 'Rental request updated successfully' }));
            }
          );
        });
      });
    }
    // Get Notifications by User Endpoint
    else if (method === 'GET' && pathname === '/notifications') {
      const { userID } = parsedUrl.query;
      if (!userID) {
        res.statusCode = 400;
        res.end(JSON.stringify({ error: 'userID is required' }));
        return;
      }

      const query = `
        SELECT n.*, rr.fullName AS tenantName, bd.name AS houseName
        FROM Notifications n
        LEFT JOIN RentalRequests rr ON n.requestID = rr.requestID
        LEFT JOIN BoardingDetails bd ON n.houseID = bd.houseID
        WHERE n.userID = ?
      `;
      db.query(query, [userID], (err, results) => {
        if (err) {
          console.error('Error retrieving notifications:', err);
          res.statusCode = 500;
          res.end(JSON.stringify({ error: 'Error retrieving notifications' }));
          return;
        }
        res.statusCode = 200;
        res.end(JSON.stringify({ notifications: results }));
      });
    }
    // Mark Notification as Read Endpoint
    else if (method === 'PUT' && pathname.match(/^\/notifications\/\d+$/)) {
      const notificationID = pathname.split('/')[2];
      const { read } = data;
      if (typeof read !== 'boolean') {
        res.statusCode = 400;
        res.end(JSON.stringify({ error: 'read must be a boolean' }));
        return;
      }

      const query = 'UPDATE Notifications SET `read` = ? WHERE notificationID = ?';
      db.query(query, [read, notificationID], (err, results) => {
        if (err) {
          console.error('Error updating notification read status:', err);
          res.statusCode = 500;
          res.end(JSON.stringify({ error: 'Error updating notification' }));
          return;
        }
        if (results.affectedRows === 0) {
          res.statusCode = 404;
          res.end(JSON.stringify({ error: 'Notification not found' }));
          return;
        }
        res.statusCode = 200;
        res.end(JSON.stringify({ message: 'Notification updated successfully' }));
      });
    }
    // Handle unknown endpoints
    else {
      res.statusCode = 404;
      res.end(JSON.stringify({ error: 'Endpoint not found' }));
    }
  });
});

// Start server
const PORT = 8080;
server.listen(PORT, () => {
  console.log(`API server running on http://192.168.165.222:${PORT}`);
});