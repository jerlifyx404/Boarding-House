import React, { useState, useEffect } from 'react';
import { View, Text, FlatList, TouchableOpacity, StyleSheet, Alert } from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import AsyncStorage from '@react-native-async-storage/async-storage';

const OwnerDetails = () => {
  const router = useRouter();
  const [boardingDetails, setBoardingDetails] = useState([]);
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
          setBoardingDetails(
            data.boardingHouses.map(house => ({
              id: house.houseID.toString(),
              BH: house.name,
              ownerName: house.ownerName,
              address: house.address,
              rooms: house.NumberOfRooms,
              phone: house.pNum,
              rent: house.price !== undefined && house.price !== null ? Number(house.price).toFixed(2) : '0.00',
              photos: house.photos,
            }))
          );
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

  const handleDelete = async (id) => {
    try {
      const response = await fetch(`${BASE_URL}/boarding/${id}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
        },
      });

      const data = await response.json();
      if (response.ok) {
        setBoardingDetails(prev => prev.filter(item => item.id !== id));
        Alert.alert('Success', 'Boarding house deleted successfully');
      } else {
        Alert.alert('Error', data.error || 'Failed to delete boarding house');
      }
    } catch (error) {
      console.error('Delete error:', error);
      Alert.alert('Error', 'Network error, please try again');
    }
  };

  const renderBoardingDetail = ({ item }) => (
    <View style={styles.detailContainer}>
      <View style={styles.detailTextContainer}>
        <Text style={styles.detailText}>BH: {item.BH}</Text>
        <Text style={styles.detailText}>Owner: {item.ownerName}</Text>
        <Text style={styles.detailText}>Address: {item.address}</Text>
        <Text style={styles.detailText}>Rooms: {item.rooms}</Text>
        <Text style={styles.detailText}>Phone: {item.phone}</Text>
        <Text style={styles.detailText}>Rent: ₱{item.rent}</Text>
      </View>
      <View style={styles.actionsContainer}>
        <TouchableOpacity
          onPress={() =>
            router.push({
              pathname: '/BoardingHouse/AddBoardingDetails',
              params: { action: 'edit', detail: JSON.stringify(item) },
            })
          }
          style={styles.actionButton}
        >
          <MaterialIcons name="edit" size={24} color="#543A14" />
        </TouchableOpacity>
        <TouchableOpacity
          onPress={() => handleDelete(item.id)}
          style={styles.actionButton}
        >
          <MaterialIcons name="delete" size={24} color="#543A14" />
        </TouchableOpacity>
      </View>
    </View>
  );

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.push('/BoardingHouse/Owner')}>
          <MaterialIcons name="arrow-back" size={30} color="#543A14" />
        </TouchableOpacity>
        <Text style={styles.title}>Owner Details</Text>
      </View>

      <TouchableOpacity
        style={styles.addButton}
        onPress={() => router.push('/BoardingHouse/AddBoardingDetails')}
      >
        <Text style={styles.addButtonText}>Add Boarding Detail</Text>
      </TouchableOpacity>

      <FlatList
        data={boardingDetails}
        renderItem={renderBoardingDetail}
        keyExtractor={item => item.id}
        style={styles.list}
        ListEmptyComponent={<Text style={styles.emptyText}>No boarding details available.</Text>}
      />
    </View>
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
  addButton: {
    backgroundColor: '#543A14',
    borderRadius: 25,
    paddingVertical: 10,
    marginHorizontal: 20,
    marginVertical: 10,
    alignItems: 'center',
  },
  addButtonText: {
    fontSize: 16,
    fontWeight: '600',
    color: '#ffff',
  },
  list: {
    flex: 1,
  },
  detailContainer: {
    flexDirection: 'row',
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
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  detailTextContainer: {
    flex: 1,
  },
  detailText: {
    fontSize: 14,
    color: '#543A14',
    marginBottom: 5,
  },
  actionsContainer: {
    flexDirection: 'row',
  },
  actionButton: {
    marginLeft: 10,
  },
  emptyText: {
    fontSize: 16,
    color: '#543A14',
    textAlign: 'center',
    marginTop: 20,
  },
});

export default OwnerDetails;