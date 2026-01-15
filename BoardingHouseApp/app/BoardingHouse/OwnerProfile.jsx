import React, { useState, useEffect } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, Alert } from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import AsyncStorage from '@react-native-async-storage/async-storage';

export default function OwnerProfile() {
  const router = useRouter();
  const [ownerData, setOwnerData] = useState({
    userID: '',
    fullName: '',
    username: '',
    email: '',
    userType: 'Owner',
  });
  const [isEditing, setIsEditing] = useState(false);
  const [password, setPassword] = useState('');

  useEffect(() => {
    const fetchProfile = async () => {
      try {
        const userID = await AsyncStorage.getItem('userID');
        if (!userID) {
          Alert.alert('Error', 'User not logged in');
          return;
        }

        const response = await fetch(`http://192.168.165.222:8080/users/${userID}`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
          },
        });

        const data = await response.json();
        if (response.ok) {
          setOwnerData(data.user);
        } else {
          Alert.alert('Error', data.error || 'Failed to fetch profile');
        }
      } catch (error) {
        console.error('Fetch error:', error);
        Alert.alert('Error', 'Network error, please try again');
      }
    };

    fetchProfile();
  }, []);

  const handleUpdate = async () => {
    if (!ownerData.fullName || !ownerData.username || !ownerData.email) {
      Alert.alert('Error', 'All fields are required');
      return;
    }

    try {
      const updateData = { ...ownerData };
      if (password) {
        updateData.password = password;
      }

      const response = await fetch(`http://192.168.165.222:8080/users/${ownerData.userID}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(updateData),
      });

      const data = await response.json();
      if (response.ok) {
        Alert.alert('Success', 'Profile updated successfully');
        setIsEditing(false);
        setPassword('');
      } else {
        Alert.alert('Error', data.error || 'Failed to update profile');
      }
    } catch (error) {
      console.error('Update error:', error);
      Alert.alert('Error', 'Network error, please try again');
    }
  };

  const handleLogout = async () => {
    await AsyncStorage.removeItem('userID');
    router.push('/BoardingHouse/Login');
  };

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()}>
          <MaterialIcons name="arrow-back" size={24} color="#543A14" />
        </TouchableOpacity>
        <Text style={styles.title}>Profile</Text>
      </View>

      <View style={styles.iconContainer}>
        <MaterialIcons name="person" size={80} color="#543A14" />
      </View>

      <View style={styles.detailsContainer}>
        <Text style={styles.sectionTitle}>Personal Details</Text>
        <Text style={styles.label}>Full Name</Text>
        <TextInput
          style={styles.input}
          value={ownerData.fullName}
          onChangeText={(text) => setOwnerData({ ...ownerData, fullName: text })}
          editable={isEditing}
        />
        <Text style={styles.label}>Username</Text>
        <TextInput
          style={styles.input}
          value={ownerData.username}
          onChangeText={(text) => setOwnerData({ ...ownerData, username: text })}
          editable={isEditing}
        />
        <Text style={styles.label}>Email Address</Text>
        <TextInput
          style={styles.input}
          value={ownerData.email}
          onChangeText={(text) => setOwnerData({ ...ownerData, email: text })}
          editable={isEditing}
        />
        {isEditing && (
          <>
            <Text style={styles.label}>New Password (optional)</Text>
            <TextInput
              style={styles.input}
              value={password}
              onChangeText={setPassword}
              secureTextEntry
              placeholder="Enter new password"
            />
          </>
        )}
      </View>

      {isEditing ? (
        <View style={styles.buttonContainer}>
          <TouchableOpacity style={styles.saveButton} onPress={handleUpdate}>
            <Text style={styles.buttonText}>Save</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.cancelButton} onPress={() => setIsEditing(false)}>
            <Text style={styles.buttonText}>Cancel</Text>
          </TouchableOpacity>
        </View>
      ) : (
        <TouchableOpacity style={styles.editButton} onPress={() => setIsEditing(true)}>
          <Text style={styles.buttonText}>Edit Profile</Text>
        </TouchableOpacity>
      )}

      <TouchableOpacity style={styles.logoutButton} onPress={handleLogout}>
        <Text style={styles.buttonText}>Logout</Text>
      </TouchableOpacity>
    </View>
  );
}

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
  iconContainer: {
    alignItems: 'center',
    marginVertical: 20,
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
  buttonContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginHorizontal: 20,
    marginTop: 20,
  },
  saveButton: {
    backgroundColor: '#543A14',
    borderRadius: 25,
    paddingVertical: 10,
    flex: 1,
    alignItems: 'center',
    marginRight: 10,
  },
  cancelButton: {
    backgroundColor: '#D3C8A5',
    borderRadius: 25,
    paddingVertical: 10,
    flex: 1,
    alignItems: 'center',
  },
  editButton: {
    backgroundColor: '#543A14',
    borderRadius: 25,
    paddingVertical: 10,
    alignItems: 'center',
    marginHorizontal: 20,
    marginTop: 20,
  },
  logoutButton: {
    backgroundColor: '#543A14',
    borderRadius: 25,
    paddingVertical: 10,
    alignItems: 'center',
    marginHorizontal: 20,
    marginTop: 20,
  },
  buttonText: {
    fontSize: 16,
    fontWeight: '600',
    color: '#FFF',
  },
});