import React, { useState, useEffect } from 'react';
import { View, Text, FlatList, Image, StyleSheet, TouchableOpacity, SafeAreaView, TextInput } from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { Alert } from 'react-native';

const Owner = () => {
  const router = useRouter();
  const [posts, setPosts] = useState([]);
  const [filteredPosts, setFilteredPosts] = useState([]);
  const [searchQuery, setSearchQuery] = useState('');
  const BASE_URL = 'http://192.168.165.222:8080';

  useEffect(() => {
    const fetchBoardingHouses = async () => {
      try {
        const userID = await AsyncStorage.getItem('userID');
        if (!userID) {
          Alert.alert('Error', 'User not logged in');
          return;
        }

        const response = await fetch(`${BASE_URL}/boarding/owner?ownerID=${userID}`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
          },
        });

        const data = await response.json();
        if (response.ok) {
          const boardingHouses = data.boardingHouses.map(house => {
            const photos = house.photos
              ? house.photos.map(url => {
                  console.log('Photo URL:', url);
                  return url && url.startsWith('http') ? url : 'https://placehold.co/150x150';
                })
              : ['https://placehold.co/150x150'];

            return {
              id: house.houseID.toString(),
              BH: house.name,
              image: photos[0],
              photos,
              ownerName: house.ownerName,
              address: house.address,
              rooms: house.NumberOfRooms,
              phone: house.pNum,
              rent: house.price !== undefined && house.price !== null ? Number(house.price).toFixed(2) : '0.00',
            };
          });
          setPosts(boardingHouses);
          setFilteredPosts(boardingHouses); // Initialize filteredPosts with all posts
          console.log('Fetched boarding houses:', boardingHouses);
        } else {
          Alert.alert('Error', data.error || 'Failed to fetch boarding houses');
        }
      } catch (error) {
        console.error('Fetch error:', error);
        Alert.alert('Error', 'Network error, please try again');
      }
    };

    fetchBoardingHouses();
  }, []);

  // Handle search input and filter posts
  const handleSearch = (query) => {
    setSearchQuery(query);
    if (!query.trim()) {
      setFilteredPosts(posts); // Reset to all posts if query is empty
      return;
    }

    const lowerQuery = query.toLowerCase();
    const filtered = posts.filter(
      (post) =>
        post.BH.toLowerCase().includes(lowerQuery) ||
        post.address.toLowerCase().includes(lowerQuery)
    );
    setFilteredPosts(filtered);
  };

  const renderPost = ({ item }) => (
    <TouchableOpacity
      style={styles.postContainer}
      onPress={() =>
        router.push({
          pathname: '/BoardingHouse/BoardingDetails',
          params: { ...item, photos: JSON.stringify(item.photos) },
        })
      }
    >
      <Image
        source={{ uri: item.image }}
        style={styles.postImage}
        onError={e => {
          console.log('Image load error:', e.nativeEvent.error, 'URL:', item.image);
          item.image = 'https://placehold.co/150x150';
        }}
        defaultSource={{ uri: 'https://placehold.co/150x150' }}
      />
      <Text style={styles.postText}>{item.BH}</Text>
    </TouchableOpacity>
  );

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <View style={styles.searchContainer}>
          <MaterialIcons name="search" size={24} color="#FFF" style={styles.searchIcon} />
          <TextInput
            style={styles.searchInput}
            placeholder="Search..."
            placeholderTextColor="#CCC"
            value={searchQuery}
            onChangeText={handleSearch}
          />
        </View>
        <TouchableOpacity onPress={() => router.push('/BoardingHouse/OwnerNotif')}>
          <MaterialIcons name="notifications" size={30} color="#543A14" />
        </TouchableOpacity>
      </View>

      <Text style={styles.title}>View Post</Text>

      <FlatList
        data={filteredPosts}
        renderItem={renderPost}
        keyExtractor={item => item.id}
        style={styles.list}
        ListEmptyComponent={<Text style={styles.emptyText}>No boarding houses match your search.</Text>}
      />

      <View style={styles.bottomNav}>
        <TouchableOpacity onPress={() => router.push('/BoardingHouse/Owner')}>
          <MaterialIcons name="home" size={30} color="#543A14" />
        </TouchableOpacity>
        <TouchableOpacity onPress={() => router.push('/BoardingHouse/OwnerDetails')}>
          <MaterialIcons name="view-list" size={30} color="#543A14" />
        </TouchableOpacity>
        <TouchableOpacity onPress={() => router.push('/BoardingHouse/OwnerProfile')}>
          <MaterialIcons name="person" size={30} color="#543A14" />
        </TouchableOpacity>
      </View>
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F7F7F7',
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingTop: 40,
    paddingBottom: 10,
    backgroundColor: 'transparent',
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    zIndex: 1,
  },
  searchContainer: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#543A14',
    borderRadius: 25,
    height: 40,
    marginRight: 10,
    paddingHorizontal: 15,
  },
  searchIcon: {
    marginRight: 10,
  },
  searchInput: {
    flex: 1,
    color: '#FFF',
    fontSize: 16,
    height: '100%',
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#543A14',
    textAlign: 'center',
    marginVertical: 15,
    marginTop: 90,
  },
  list: {
    flex: 1,
  },
  postContainer: {
    backgroundColor: '#FFF',
    borderRadius: 15,
    marginHorizontal: 20,
    marginVertical: 10,
    padding: 15,
    elevation: 3,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 3,
  },
  postImage: {
    width: '100%',
    height: 200,
    borderRadius: 10,
    marginBottom: 10,
  },
  postText: {
    fontSize: 16,
    fontWeight: '600',
    color: '#543A14',
  },
  bottomNav: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    paddingVertical: 10,
    backgroundColor: '#FFF',
    borderTopWidth: 2,
    borderTopColor: '#543A14',
    borderBottomWidth: 2,
    borderBottomColor: '#543A14',
    paddingBottom: 10,
    marginBottom: 37,
  },
  emptyText: {
    fontSize: 16,
    color: '#543A14',
    textAlign: 'center',
    marginTop: 20,
  },
});

export default Owner;