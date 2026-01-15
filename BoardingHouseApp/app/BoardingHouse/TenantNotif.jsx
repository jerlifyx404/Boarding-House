import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, Alert, TouchableOpacity } from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';
import { useRouter, useLocalSearchParams } from 'expo-router';
import AsyncStorage from '@react-native-async-storage/async-storage';

const TenantNotif = () => {
  const router = useRouter();
  const [notifications, setNotifications] = useState([]);
  const [userID, setUserID] = useState(null);
  const BASE_URL = 'http://192.168.165.222:8080';

  useEffect(() => {
    const checkLogin = async () => {
      try {
        const id = await AsyncStorage.getItem('userID');
        if (!id) {
          Alert.alert('Error', 'Please log in to view notifications.');
          router.push('/BoardingHouse/Login');
          return;
        }
        setUserID(id);
      } catch (error) {
        console.error('Error checking login:', error);
      }
    };
    checkLogin();
  }, []);

  useEffect(() => {
    if (userID) {
      fetchNotifications();
    }
  }, [userID]);

  const fetchNotifications = async () => {
    try {
      const response = await fetch(`${BASE_URL}/notifications?userID=${userID}`, {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' },
      });
      const data = await response.json();
      if (response.ok) {
        setNotifications(data.notifications || []);
      } else {
        Alert.alert('Error', data.error || 'Failed to fetch notifications');
      }
    } catch (error) {
      console.error('Fetch error:', error);
      Alert.alert('Error', 'Network error, please try again');
    }
  };

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()}>
          <MaterialIcons name="arrow-back" size={30} color="#543A14" />
        </TouchableOpacity>
        <Text style={styles.title}>Notification</Text>
      </View>
      {notifications.map((notif) => (
        <View key={notif.notificationID} style={styles.notificationCard}>
          <Text style={styles.notificationText}>
            Request for {notif.houseName} is {notif.status || 'pending'}
          </Text>
        </View>
      ))}
    </View>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#F7F7F7', paddingTop: 40 },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingBottom: 10,
    backgroundColor: 'transparent',
  },
  title: { fontSize: 24, fontWeight: 'bold', color: '#543A14', marginLeft: 10 },
  notificationCard: {
    backgroundColor: '#543A14',
    borderRadius: 10,
    margin: 10,
    padding: 15,
    elevation: 3,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 3,
  },
  notificationText: { color: '#FFF', fontSize: 16 },
});

export default TenantNotif;