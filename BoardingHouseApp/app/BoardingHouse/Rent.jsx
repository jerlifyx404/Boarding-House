import React, { useState, useEffect } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, Alert } from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';
import { useNavigation, useLocalSearchParams } from 'expo-router';
import AsyncStorage from '@react-native-async-storage/async-storage';

const Rent = () => {
  const [formData, setFormData] = useState({
    fullName: '',
    mobileNumber: '',
    email: '',
    roomPreference: '',
  });
  const [error, setError] = useState('');
  const [tenantID, setTenantID] = useState(null);
  const navigation = useNavigation();
  const params = useLocalSearchParams();
  const houseID = params.houseID;

  // Load tenantID from AsyncStorage
  useEffect(() => {
    const loadTenantID = async () => {
      try {
        const userID = await AsyncStorage.getItem('userID');
        if (userID) {
          setTenantID(userID);
        } else {
          Alert.alert('Error', 'Please log in to proceed.');
          navigation.replace('/BoardingHouse/Login');
        }
      } catch (err) {
        console.error('Error loading userID from AsyncStorage:', err);
        Alert.alert('Error', 'An error occurred. Please try again.');
        navigation.goBack();
      }
    };
    loadTenantID();
  }, []);

  // Debug params and tenantID
  useEffect(() => {
    console.log('Params:', params);
    console.log('TenantID:', tenantID);
    console.log('HouseID:', houseID);
    if (tenantID && !houseID) {
      Alert.alert('Error', 'Missing house information. Please try again.');
      navigation.goBack();
    }
  }, [tenantID, houseID]);

  const handleChange = (name, value) => {
    setFormData({ ...formData, [name]: value });
  };

  const handleSubmit = async () => {
    if (!formData.fullName || !formData.mobileNumber || !formData.email || !formData.roomPreference) {
      setError('All fields are required');
      return;
    }
    if (!['Single Room', 'Shared Room'].includes(formData.roomPreference)) {
      setError('Room preference must be Single Room or Shared Room');
      return;
    }
    if (!tenantID || !houseID) {
      setError('Tenant ID or House ID is missing');
      return;
    }

    try {
      const response = await fetch('http://192.168.165.222:8080/rental-requests', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          tenantID,
          houseID,
          fullName: formData.fullName,
          mobileNumber: formData.mobileNumber,
          email: formData.email,
          roomPreference: formData.roomPreference,
        }),
      });

      const result = await response.json();
      if (response.ok) {
        setError('');
        Alert.alert('Success', 'Rental request submitted successfully');
        navigation.goBack();
      } else {
        setError(result.error || 'Failed to submit rental request');
      }
    } catch (err) {
      console.error('Error submitting rental request:', err);
      setError('An error occurred while submitting the request');
    }
  };

  if (!tenantID || !houseID) return null;

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <MaterialIcons name="arrow-back" size={24} color="#543A14" />
        </TouchableOpacity>
        <Text style={styles.title}>Rent</Text>
      </View>

      <View style={styles.detailsContainer}>
        <Text style={styles.sectionTitle}>Personal Details</Text>
        <Text style={styles.label}>Full Name</Text>
        <TextInput
          style={styles.input}
          value={formData.fullName}
          onChangeText={(text) => handleChange('fullName', text)}
          placeholder="Enter full name"
          placeholderTextColor="#888"
        />
        <Text style={styles.label}>Mobile Number</Text>
        <TextInput
          style={styles.input}
          value={formData.mobileNumber}
          onChangeText={(text) => handleChange('mobileNumber', text)}
          placeholder="Enter mobile number"
          keyboardType="phone-pad"
          placeholderTextColor="#888"
        />
        <Text style={styles.label}>Email Address</Text>
        <TextInput
          style={styles.input}
          value={formData.email}
          onChangeText={(text) => handleChange('email', text)}
          placeholder="Enter email address"
          keyboardType="email-address"
          autoCapitalize="none"
          placeholderTextColor="#888"
        />

        <Text style={styles.sectionTitle}>Rental Details</Text>
        <Text style={styles.label}>Room Preference</Text>
        <TextInput
          style={styles.input}
          value={formData.roomPreference}
          onChangeText={(text) => handleChange('roomPreference', text)}
          placeholder="Single Room/Shared Room"
          placeholderTextColor="#888"
        />

        <Text style={styles.label}>Payment Method</Text>
        <View style={styles.paymentContainer}>
          <Text style={styles.paymentText}>In-person only</Text>
        </View>
      </View>

      {error ? <Text style={styles.error}>{error}</Text> : null}

      <TouchableOpacity style={styles.button} onPress={handleSubmit}>
        <Text style={styles.buttonText}>Rent</Text>
      </TouchableOpacity>
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
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#543A14',
    marginLeft: 10,
  },
  detailsContainer: {
    paddingHorizontal: 20,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#543A14',
    marginBottom: 15,
  },
  label: {
    fontSize: 16,
    color: '#543A14',
    marginBottom: 5,
  },
  input: {
    height: 40,
    borderWidth: 1,
    borderColor: '#D3C8A5',
    borderRadius: 5,
    backgroundColor: '#FFF5E6',
    marginBottom: 15,
    paddingHorizontal: 10,
    fontSize: 16,
    color: '#543A14',
  },
  paymentContainer: {
    height: 40,
    borderWidth: 1,
    borderColor: '#D3C8A5',
    borderRadius: 5,
    backgroundColor: '#FFF5E6',
    marginBottom: 15,
    justifyContent: 'center',
    paddingHorizontal: 10,
  },
  paymentText: {
    fontSize: 16,
    color: '#543A14',
  },
  error: {
    color: 'red',
    fontSize: 16,
    marginTop: 10,
    paddingHorizontal: 20,
  },
  button: {
    backgroundColor: '#543A14',
    borderRadius: 25,
    paddingVertical: 10,
    alignItems: 'center',
    marginHorizontal: 20,
    marginTop: 20,
    marginBottom: 20,
  },
  buttonText: {
    fontSize: 16,
    fontWeight: '600',
    color: '#FFF',
  },
});

export default Rent;