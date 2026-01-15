import React, { useEffect } from 'react';
import { View, Text, Image, StyleSheet, TouchableOpacity, ScrollView, FlatList, Linking } from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';
import { useRouter, useLocalSearchParams } from 'expo-router';
import AsyncStorage from '@react-native-async-storage/async-storage';

const TenantDetails = () => {
  const router = useRouter();
  const { id, BH, image, ownerName, address, rooms, phone, rent, photos } = useLocalSearchParams();

  const validImage = image && image !== 'undefined' && image !== '' ? image : 'https://placehold.co/150x150';

  let photoList = [];
  try {
    photoList = photos && photos !== 'undefined' ? JSON.parse(photos) : [];
  } catch (e) {
    console.error('Error parsing photos:', e);
    photoList = [];
  }
  photoList = photoList.filter(url => url && typeof url === 'string' && url.trim() !== '');

  // Check if user is logged in
  useEffect(() => {
    const checkLogin = async () => {
      try {
        const userID = await AsyncStorage.getItem('userID');
        if (!userID) {
          Alert.alert('Error', 'Please log in to proceed.');
          router.push('/BoardingHouse/Login');
        }
      } catch (error) {
        console.error('Error checking login:', error);
      }
    };
    checkLogin();
  }, []);

  const openGoogleMaps = () => {
    const url = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}`;
    Linking.openURL(url).catch(err => console.error('Error opening Google Maps:', err));
  };

  const handleRent = async () => {
    try {
      const userID = await AsyncStorage.getItem('userID');
      if (!userID) {
        Alert.alert('Error', 'Please log in to proceed.');
        router.push('/BoardingHouse/Login');
        return;
      }
      router.push({
        pathname: '/BoardingHouse/Rent',
        params: { houseID: id },
      });
    } catch (error) {
      console.error('Error checking userID:', error);
      Alert.alert('Error', 'An error occurred. Please try again.');
    }
  };

  const renderPhoto = ({ item }) => (
    <Image
      source={{ uri: item }}
      style={styles.photo}
      onError={(e) => console.log('Photo load error:', e.nativeEvent.error, 'URL:', item)}
      defaultSource={{ uri: 'https://placehold.co/100x100' }}
    />
  );

  return (
    <ScrollView style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()}>
          <MaterialIcons name="arrow-back" size={30} color="#543A14" />
        </TouchableOpacity>
        <Text style={styles.title}>Tenant Details</Text>
      </View>

      <View style={styles.postContainer}>
        <Image
          source={{ uri: validImage }}
          style={styles.postImage}
          onError={(e) => console.log('Main image load error:', e.nativeEvent.error, 'URL:', validImage)}
          defaultSource={{ uri: 'https://placehold.co/150x150' }}
        />
        <Text style={styles.postText}>{BH}</Text>
      </View>

      <View style={styles.detailsContainer}>
        <Text style={styles.detailsText}>Name of the Owner: {ownerName}</Text>
        <Text style={styles.detailsText}>Address: {address}</Text>
        <Text style={styles.detailsText}>Number of Rooms: {rooms}</Text>
        <Text style={styles.detailsText}>Phone Number: {phone}</Text>
        <Text style={styles.detailsText}>Rent: ₱{rent}</Text>
      </View>

      <View style={styles.photosContainer}>
        {photoList.length > 0 ? (
          <>
            <Text style={styles.photosTitle}>Photos</Text>
            <FlatList
              data={photoList}
              renderItem={renderPhoto}
              keyExtractor={(item, index) => index.toString()}
              horizontal
              showsHorizontalScrollIndicator={false}
            />
          </>
        ) : (
          <Text style={styles.noPhotosText}>No additional photos available.</Text>
        )}
      </View>

      <View style={styles.buttonContainer}>
        <TouchableOpacity style={[styles.button, styles.locationButton]} onPress={openGoogleMaps}>
          <Text style={styles.buttonText}>View Location</Text>
        </TouchableOpacity>
        <TouchableOpacity style={[styles.button, styles.rentButton]} onPress={handleRent}>
          <Text style={styles.buttonText}>Rent</Text>
        </TouchableOpacity>
      </View>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F7F7F7',
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingTop: 40,
    paddingBottom: 10,
    backgroundColor: 'transparent',
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#543A14',
    marginLeft: 10,
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
  detailsContainer: {
    backgroundColor: '#FFF8E7',
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
  detailsText: {
    fontSize: 16,
    color: '#543A14',
    marginBottom: 5,
  },
  photosContainer: {
    marginHorizontal: 20,
    marginVertical: 10,
  },
  photosTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#543A14',
    marginBottom: 10,
  },
  photo: {
    width: 100,
    height: 100,
    borderRadius: 10,
    marginRight: 10,
  },
  noPhotosText: {
    fontSize: 16,
    color: '#543A14',
    textAlign: 'center',
    marginVertical: 10,
  },
  buttonContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginHorizontal: 20,
    marginVertical: 20,
  },
  button: {
    flex: 1,
    borderRadius: 25,
    paddingVertical: 12,
    alignItems: 'center',
  },
  locationButton: {
    backgroundColor: '#543A14',
    marginRight: 10,
  },
  rentButton: {
    backgroundColor: '#543A14',
  },
  buttonText: {
    fontSize: 16,
    fontWeight: '600',
    color: '#FFF',
  },
});

export default TenantDetails;