import React from 'react';
import { View, Text, Image, StyleSheet, TouchableOpacity, ScrollView, FlatList, Linking } from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';
import { useRouter, useLocalSearchParams } from 'expo-router';

const BoardingDetails = () => {
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

  const openGoogleMaps = () => {
    const url = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}`;
    Linking.openURL(url).catch(err => console.error('Error opening Google Maps:', err));
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
        <Text style={styles.title}>Boarding Details</Text>
        
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

      <TouchableOpacity style={styles.locationButton} onPress={openGoogleMaps}>
        <Text style={styles.locationButtonText}>View Location</Text>
      </TouchableOpacity>
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
  locationButton: {
    backgroundColor: '#543A14',
    borderRadius: 25,
    paddingVertical: 12,
    marginHorizontal: 20,
    marginVertical: 20,
    alignItems: 'center',
  },
  locationButtonText: {
    fontSize: 16,
    fontWeight: '600',
    color: '#FFF',
  },
});

export default BoardingDetails; 