// Firebase configuration and initialization
import { initializeApp } from 'firebase/app';
import { getAuth } from 'firebase/auth';
import { getFirestore } from 'firebase/firestore';

const firebaseConfig = {
  apiKey: import.meta.env.VITE_FIREBASE_API_KEY || "AIzaSyAiwMHCsSrqX20x8o90R5hksRxUPVmnwKM",
  authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN || "hthree-94a3e.firebaseapp.com",
  projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID || "hthree-94a3e",
  storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET || "hthree-94a3e.firebasestorage.app",
  messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID || "917487991713",
  appId: import.meta.env.VITE_FIREBASE_APP_ID || "1:917487991713:web:68dd73293f38d7fe2229e0",
  measurementId: import.meta.env.VITE_FIREBASE_MEASUREMENT_ID || "G-9LNBDMVK0M"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);

// Initialize services
export const auth = getAuth(app);
export const db = getFirestore(app);

export default app;
