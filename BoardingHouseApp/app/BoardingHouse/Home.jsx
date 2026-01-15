import { View, Text, Image, StyleSheet, TouchableOpacity } from 'react-native';
import React from 'react';
import { useRouter } from 'expo-router';

const Home = () => {
  const router = useRouter(); // Get the router object for navigation

  return (
    <View style={styles.container}>
      {/* Location Pin */}
      <Image
        source={require('../assets/img/location.png')} // Unchanged as requested
        style={styles.location}
      />
      {/* House Icon */}
      <Image
        source={require('../assets/img/house.png')} // Unchanged as requested
        style={styles.house}
      />
      {/* Text */}
      <TouchableOpacity onPress={() => router.push('/BoardingHouse/Login')}>
        <Text style={styles.text}>Easier Boarding, Better Living!</Text>
      </TouchableOpacity>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F5E9D4', // Light beige background color from the image
    justifyContent: 'center',
    alignItems: 'center',
  },
  location: {
    width: 60, // Scaled down from 256 to fit mobile screen
    height: 60, // Maintain aspect ratio
    position: 'absolute',
    top: 180, // Moved closer to the top to match the image
    right: 110,
  },
  house: {
    width: 200, // Adjusted size to better match the image proportions
    height: 200, // Maintain aspect ratio
    marginBottom: 40, // Increased spacing between house and text
  },
  text: {
    fontSize: 26, // Adjusted font size to match the image
    fontWeight: 'bold',
    color: '#000',
    textAlign: 'center',
  },
});

export default Home;