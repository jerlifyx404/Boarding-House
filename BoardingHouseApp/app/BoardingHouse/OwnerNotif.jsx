import React, { useState, useEffect, useCallback } from 'react';
import { View, Text, TouchableOpacity, StyleSheet, ScrollView, ActivityIndicator, Alert, RefreshControl } from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import AsyncStorage from '@react-native-async-storage/async-storage';

const OwnerNotif = () => {
  const router = useRouter();
  const [rentalRequests, setRentalRequests] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [userID, setUserID] = useState(null);

  // Fetch userID from AsyncStorage
  useEffect(() => {
    const getUserID = async () => {
      try {
        const id = await AsyncStorage.getItem('userID');
        if (id) {
          setUserID(id);
        } else {
          Alert.alert('Error', 'User not logged in');
          router.replace('/login'); // Adjust to your login route
        }
      } catch (error) {
        Alert.alert('Error', 'Failed to retrieve user data');
        console.error('AsyncStorage error:', error);
      }
    };
    getUserID();
  }, []);

  // Fetch pending rental requests
  const fetchRentalRequests = useCallback(async () => {
    if (!userID) return;
    try {
      setLoading(true);
      const response = await fetch(`http://192.168.165.222:8080/rental-requests/owner?ownerID=${userID}`);
      const data = await response.json();
      if (response.ok) {
        setRentalRequests(data.rentalRequests);
      } else {
        Alert.alert('Error', data.error || 'Failed to fetch rental requests');
      }
    } catch (error) {
      Alert.alert('Error', 'Network error occurred');
      console.error('Fetch error:', error);
    } finally {
      setLoading(false);
    }
  }, [userID]);

  useEffect(() => {
    fetchRentalRequests();
  }, [fetchRentalRequests]);

  // Handle pull-to-refresh
  const onRefresh = useCallback(async () => {
    setRefreshing(true);
    await fetchRentalRequests();
    setRefreshing(false);
  }, [fetchRentalRequests]);

  // Handle approve/decline with confirmation
  const handleStatusUpdate = async (requestID, status) => {
    Alert.alert(
      `Confirm ${status.charAt(0).toUpperCase() + status.slice(1)}`,
      `Are you sure you want to ${status} this request?`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Confirm',
          onPress: async () => {
            try {
              const response = await fetch(`http://192.168.165.222:8080/rental-requests/${requestID}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status }),
              });
              const data = await response.json();
              if (response.ok) {
                setRentalRequests(rentalRequests.filter(req => req.requestID !== requestID));
                Alert.alert('Success', `Request ${status} successfully`);
              } else {
                Alert.alert('Error', data.error || `Failed to ${status} request`);
              }
            } catch (error) {
              Alert.alert('Error', 'Network error occurred');
              console.error('Update error:', error);
            }
          },
        },
      ]
    );
  };

  return (
    <ScrollView
      style={styles.container}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
    >
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()}>
          <MaterialIcons name="arrow-back" size={30} color="#543A14" />
        </TouchableOpacity>
        <Text style={styles.title}>Notifications</Text>
        <TouchableOpacity>
          <MaterialIcons name="notifications" size={30} color="#543A14" />
        </TouchableOpacity>
      </View>

      {loading || !userID ? (
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color="#543A14" />
        </View>
      ) : rentalRequests.length === 0 ? (
        <View style={styles.emptyContainer}>
          <Text style={styles.emptyText}>No pending rental requests</Text>
        </View>
      ) : (
        <View style={styles.notificationContainer}>
          {rentalRequests.map(request => (
            <View key={request.requestID} style={styles.notificationCard}>
              <Text style={styles.notificationText}>
                {request.fullName} requested to rent {request.houseName}
              </Text>
              <Text style={styles.detailText}>Status: {request.status}</Text>
              <Text style={styles.detailText}>Room Preference: {request.roomPreference}</Text>
              <Text style={styles.detailText}>Contact: {request.mobileNumber}</Text>
              <View style={styles.buttonContainer}>
                <TouchableOpacity
                  style={[styles.button, styles.approveButton]}
                  onPress={() => handleStatusUpdate(request.requestID, 'approved')}
                >
                  <Text style={styles.buttonText}>Approve</Text>
                </TouchableOpacity>
                <TouchableOpacity
                  style={[styles.button, styles.declineButton]}
                  onPress={() => handleStatusUpdate(request.requestID, 'declined')}
                >
                  <Text style={styles.buttonText}>Decline</Text>
                </TouchableOpacity>
              </View>
            </View>
          ))}
        </View>
      )}
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
    justifyContent: 'space-between',
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#543A14',
  },
  notificationContainer: {
    flex: 1,
    paddingHorizontal: 10,
  },
  notificationCard: {
    backgroundColor: '#8B5E3C',
    borderRadius: 10,
    marginVertical: 10,
    padding: 15,
    elevation: 3,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 3,
  },
  notificationText: {
    color: '#FFFFFF',
    fontSize: 16,
    marginBottom: 5,
    fontWeight: '500',
  },
  detailText: {
    color: '#E0E0E0',
    fontSize: 14,
    marginBottom: 5,
    fontWeight: '400',
  },
  buttonContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginTop: 10,
  },
  button: {
    paddingVertical: 8,
    paddingHorizontal: 15,
    borderRadius: 20,
    alignItems: 'center',
    flex: 1,
    marginHorizontal: 5,
  },
  approveButton: {
    backgroundColor: '#4CAF50',
  },
  declineButton: {
    backgroundColor: '#F44336',
  },
  buttonText: {
    color: '#FFFFFF',
    fontWeight: '600',
    fontSize: 14,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 50,
  },
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 50,
  },
  emptyText: {
    fontSize: 16,
    color: '#543A14',
  },
});

export default OwnerNotif;