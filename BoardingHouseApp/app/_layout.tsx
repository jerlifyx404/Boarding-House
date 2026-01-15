// import { DarkTheme, DefaultTheme, ThemeProvider } from '@react-navigation/native';
// import { useFonts } from 'expo-font';
// import { Stack } from 'expo-router';
// import * as SplashScreen from 'expo-splash-screen';
// import { StatusBar } from 'expo-status-bar';
// import { useEffect } from 'react';
// import 'react-native-reanimated';

// import { useColorScheme } from '@/hooks/useColorScheme';

// // Prevent the splash screen from auto-hiding before asset loading is complete.
// SplashScreen.preventAutoHideAsync();

// export default function RootLayout() {
//   const colorScheme = useColorScheme();
//   const [loaded] = useFonts({
//     SpaceMono: require('../assets/fonts/SpaceMono-Regular.ttf'),
//   });

//   useEffect(() => {
//     if (loaded) {
//       SplashScreen.hideAsync();
//     }
//   }, [loaded]);

//   if (!loaded) {
//     return null;
//   }

//   return (
//     <ThemeProvider value={colorScheme === 'dark' ? DarkTheme : DefaultTheme}>
//       <Stack>
//         <Stack.Screen name="(tabs)" options={{ headerShown: false }} />
//         <Stack.Screen name="+not-found" />
//       </Stack>
//       <StatusBar style="auto" />
//     </ThemeProvider>
//   );
// }
import { Stack } from 'expo-router';

export default function Layout() {
  return (
    <Stack
      screenOptions={{
        headerShown: false, // Hides the header for all screens
      }}
    >
      <Stack.Screen name="BoardingHouse/Home" options={{ title: 'Home' }} />
      <Stack.Screen name="BoardingHouse/Login" options={{ title: 'Login' }} />
      <Stack.Screen name="BoardingHouse/SignUp" options={{ title: 'Sign Up' }} />
      {/* <Stack.Screen name="BoardingHouse/Users" options={{ title: 'Users' }} /> */}
      <Stack.Screen name="BoardingHouse/Tenant" options={{ title: 'Tenant' }} />
      <Stack.Screen name="BoardingHouse/TenantDetails" options={{ title: 'TenantDetails' }} />
      <Stack.Screen name="BoardingHouse/Rent" options={{ title: 'Rent' }} />
      <Stack.Screen name="BoardingHouse/TenantNotif" options={{ title: 'TenantNotif' }} />
      <Stack.Screen name="BoardingHouse/TenantProfile" options={{ title: 'TenantProfile' }} />

      <Stack.Screen name="BoardingHouse/Owner" options={{ title: 'Owner' }} />
      <Stack.Screen name="BoardingHouse/OwnerDetails" options={{ title: 'OwnerDetails' }} />
      <Stack.Screen name="BoardingHouse/OwnerProfile" options={{ title: 'OwnerProfile' }} />
      <Stack.Screen name="BoardingHouse/BoardingDetails" options={{ title: 'BoardingDetails' }} />
      <Stack.Screen name="BoardingHouse/OwnerNotif" options={{ title: 'OwnerNotif' }} />
      <Stack.Screen name="BoardingHouse/AddBoardingDetails" options={{ title: 'AddBoardingDetails' }} />

      
    </Stack>
  );
}