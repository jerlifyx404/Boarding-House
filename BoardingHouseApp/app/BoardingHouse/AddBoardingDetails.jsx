import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  Alert,
  Image,
  FlatList,
  ScrollView,
} from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';
import { useRouter, useLocalSearchParams } from 'expo-router';
import AsyncStorage from '@react-native-async-storage/async-storage';
import * as ImagePicker from 'expo-image-picker';

const AddBoardingDetails = () => {
  const router = useRouter();
  const { action, detail } = useLocalSearchParams();
  const isEdit = action === 'edit';
  const initialDetail = detail ? JSON.parse(detail) : {};

  const [ownerName, setOwnerName] = useState('');
  const [name, setName] = useState(isEdit ? initialDetail.BH : '');
  const [address, setAddress] = useState(isEdit ? initialDetail.address : '');
  const [rooms, setRooms] = useState(isEdit ? initialDetail.rooms.toString() : '');
  const [phone, setPhone] = useState(isEdit ? initialDetail.phone : '');
  const [rent, setRent] = useState(isEdit ? initialDetail.rent : '');
  const [newPhotos, setNewPhotos] = useState([]);
  const [existingPhotos, setExistingPhotos] = useState([]);
  const [photosToDelete, setPhotosToDelete] = useState([]);

  const BASE_URL = 'http://192.168.165.222:8080';

  useEffect(() => {
    const fetchOwnerName = async () => {
      try {
        const userID = await AsyncStorage.getItem('userID');
        if (!userID) {
          Alert.alert('Error', 'User not logged in');
          return;
        }

        const response = await fetch(`${BASE_URL}/users/${userID}`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
          },
        });

        const data = await response.json();
        if (response.ok) {
          setOwnerName(data.user.fullName);
        } else {
          Alert.alert('Error', data.error || 'Failed to fetch owner details');
        }
      } catch (error) {
        console.error('Fetch owner error:', error);
        Alert.alert('Error', 'Network error while fetching owner details');
      }
    };

    const fetchExistingPhotos = async () => {
      if (isEdit) {
        try {
          const response = await fetch(`${BASE_URL}/boarding/${initialDetail.id}`, {
            method: 'GET',
            headers: {
              'Content-Type': 'application/json',
            },
          });

          const data = await response.json();
          if (response.ok) {
            const photos = data.boardingHouse.photos.map(photo => ({
              uri: photo,
              serverPath: photo.replace(`${BASE_URL}`, ''),
              markedForDeletion: false,
            }));
            setExistingPhotos(photos);
          } else {
            console.error('Failed to fetch photos:', data.error);
          }
        } catch (error) {
          console.error('Error fetching existing photos:', error);
        }
      }
    };

    fetchOwnerName();
    fetchExistingPhotos();
  }, [isEdit, initialDetail.id]);

  const pickImages = async () => {
    const { status } = await ImagePicker.requestMediaLibraryPermissionsAsync();
    if (status !== 'granted') {
      Alert.alert('Permission Denied', 'Camera roll permissions required!');
      return;
    }

    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
      allowsMultipleSelection: true,
      quality: 1,
    });

    if (!result.canceled) {
      const newImages = result.assets.map(asset => ({
        uri: asset.uri,
        name: asset.fileName || `photo-${Date.now()}.jpg`,
        type: asset.mimeType || 'image/jpeg',
      }));
      setNewPhotos(prev => [...prev, ...newImages]);
    }
  };

  const toggleDeletePhoto = (uri, serverPath) => {
    setExistingPhotos(prev =>
      prev.map(photo =>
        photo.uri === uri ? { ...photo, markedForDeletion: !photo.markedForDeletion } : photo
      )
    );
    setPhotosToDelete(prev =>
      prev.includes(serverPath) ? prev.filter(p => p !== serverPath) : [...prev, serverPath]
    );
  };

  const removeNewPhoto = uri => {
    setNewPhotos(prev => prev.filter(photo => photo.uri !== uri));
  };

  const handleSubmit = async () => {
    if (!name || !address || !rooms || !phone || !rent) {
      Alert.alert('Error', 'All fields are required');
      return;
    }

    try {
      const userID = await AsyncStorage.getItem('userID');
      if (!userID) {
        Alert.alert('Error', 'User not logged in');
        return;
      }

      const formData = new FormData();
      formData.append('ownerID', userID);
      formData.append('name', name);
      formData.append('address', address);
      formData.append('NumberOfRooms', parseInt(rooms) || 0);
      formData.append('pNum', phone);
      formData.append('price', parseFloat(rent) || 0);

      newPhotos.forEach((photo, index) => {
        formData.append('photos', {
          uri: photo.uri,
          name: photo.name,
          type: photo.type,
        });
      });

      if (isEdit && photosToDelete.length > 0) {
        formData.append('photosToDelete', JSON.stringify(photosToDelete));
      }

      const url = isEdit ? `${BASE_URL}/boarding/${initialDetail.id}` : `${BASE_URL}/boarding`;
      const method = isEdit ? 'PUT' : 'POST';

      const response = await fetch(url, {
        method,
        body: formData,
        headers: {
          Accept: 'application/json',
        },
      });

      const data = await response.json();
      if (response.ok) {
        Alert.alert('Success', isEdit ? 'Boarding house updated' : 'Boarding house created');
        router.push('/BoardingHouse/OwnerDetails');
      } else {
        Alert.alert('Error', data.error || 'Failed to save boarding house');
      }
    } catch (error) {
      console.error('Submit error:', error);
      Alert.alert('Error', `Network error: ${error.message}`);
    }
  };

  const renderNewPhoto = ({ item }) => (
    <View style={styles.photoContainer}>
      <Image
        source={{ uri: item.uri }}
        style={styles.photo}
        onError={e => console.log('New photo load error:', e.nativeEvent.error)}
      />
      <TouchableOpacity
        style={styles.deleteButton}
        onPress={() => removeNewPhoto(item.uri)}
      >
        <MaterialIcons name="close" size={20} color="#543A14" />
      </TouchableOpacity>
    </View>
  );

  const renderExistingPhoto = ({ item }) => (
    <View style={styles.photoContainer}>
      <Image
        source={{ uri: item.uri }}
        style={styles.photo}
        onError={e => console.log('Existing photo load error:', e.nativeEvent.error, 'URL:', item.uri)}
      />
      <TouchableOpacity
        style={styles.deleteButton}
        onPress={() => toggleDeletePhoto(item.uri, item.serverPath)}
      >
        <MaterialIcons
          name={item.markedForDeletion ? 'check-box' : 'check-box-outline-blank'}
          size={20}
          color="#543A14"
        />
      </TouchableOpacity>
    </View>
  );

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.scrollContent}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()}>
          <MaterialIcons name="arrow-back" size={30} color="#543A14" />
        </TouchableOpacity>
        <Text style={styles.title}>{isEdit ? 'Edit Boarding Detail' : 'Add Boarding Detail'}</Text>
      </View>

      <Text style={styles.label}>Name of the Owner:</Text>
      <TextInput
        style={[styles.input, styles.disabledInput]}
        value={ownerName}
        editable={false}
      />

      <Text style={styles.label}>Boarding House Name:</Text>
      <TextInput
        style={styles.input}
        value={name}
        onChangeText={setName}
        placeholder="Enter boarding house name"
      />

      <Text style={styles.label}>Address:</Text>
      <TextInput
        style={styles.input}
        value={address}
        onChangeText={setAddress}
        placeholder="Enter address"
      />

      <Text style={styles.label}>Number of Rooms:</Text>
      <TextInput
        style={styles.input}
        value={rooms}
        onChangeText={setRooms}
        placeholder="Enter number of rooms"
        keyboardType="numeric"
      />

      <Text style={styles.label}>Phone Number:</Text>
      <TextInput
        style={styles.input}
        value={phone}
        onChangeText={setPhone}
        placeholder="Enter phone number"
        keyboardType="phone-pad"
      />

      <Text style={styles.label}>Rent:</Text>
      <TextInput
        style={styles.input}
        value={rent}
        onChangeText={setRent}
        placeholder="Enter rent amount"
        keyboardType="numeric"
      />

      <Text style={styles.label}>Photos:</Text>
      <TouchableOpacity style={styles.uploadButton} onPress={pickImages}>
        <Text style={styles.uploadButtonText}>Choose Files</Text>
      </TouchableOpacity>

      {newPhotos.length > 0 && (
        <>
          <Text style={styles.subLabel}>New Photos:</Text>
          <FlatList
            data={newPhotos}
            renderItem={renderNewPhoto}
            keyExtractor={item => item.uri}
            horizontal
            showsHorizontalScrollIndicator={false}
            style={styles.photoList}
          />
        </>
      )}

      {isEdit && existingPhotos.length > 0 && (
        <>
          <Text style={styles.subLabel}>Existing Photos (Check to Delete):</Text>
          <FlatList
            data={existingPhotos}
            renderItem={renderExistingPhoto}
            keyExtractor={item => item.uri}
            horizontal
            showsHorizontalScrollIndicator={false}
            style={styles.photoList}
          />
        </>
      )}

      <TouchableOpacity style={styles.submitButton} onPress={handleSubmit}>
        <Text style={styles.submitButtonText}>SUBMIT</Text>
      </TouchableOpacity>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F7F7F7',
  },
  scrollContent: {
    paddingHorizontal: 20,
    paddingBottom: 20,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingTop: 40,
    paddingBottom: 10,
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#543A14',
    marginLeft: 10,
  },
  label: {
    fontSize: 16,
    color: '#543A14',
    marginBottom: 5,
    marginTop: 15,
  },
  subLabel: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#543A14',
    marginTop: 10,
    marginBottom: 5,
  },
  input: {
    height: 40,
    borderWidth: 1,
    borderColor: '#D3C8A5',
    borderRadius: 5,
    backgroundColor: '#FFF5E6',
    paddingHorizontal: 10,
    fontSize: 16,
    color: '#543A14',
  },
  disabledInput: {
    backgroundColor: '#EDE7D9',
    color: '#888',
  },
  uploadButton: {
    backgroundColor: '#D3C8A5',
    borderRadius: 5,
    paddingVertical: 10,
    alignItems: 'center',
    marginBottom: 10,
  },
  uploadButtonText: {
    fontSize: 16,
    color: '#543A14',
  },
  photoList: {
    marginBottom: 10,
  },
  photoContainer: {
    position: 'relative',
    marginRight: 10,
  },
  photo: {
    width: 100,
    height: 100,
    borderRadius: 5,
  },
  deleteButton: {
    position: 'absolute',
    top: 5,
    right: 5,
    backgroundColor: 'rgba(255, 255, 255, 0.7)',
    borderRadius: 5,
  },
  submitButton: {
    backgroundColor: '#543A14',
    borderRadius: 25,
    paddingVertical: 12,
    alignItems: 'center',
    marginTop: 20,
    marginBottom: 20,
  },
  submitButtonText: {
    fontSize: 16,
    fontWeight: '600',
    color: '#FFF',
  },
});

export default AddBoardingDetails;