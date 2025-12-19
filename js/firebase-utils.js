// Firebase Utility Functions
// This file contains helper functions for common Firebase operations

import { auth, db } from './firebase-config.js';
import { 
  createUserWithEmailAndPassword, 
  signInWithEmailAndPassword, 
  signOut, 
  onAuthStateChanged 
} from "https://www.gstatic.com/firebasejs/9.22.0/firebase-auth.js";
import { 
  collection, doc, setDoc, getDoc, getDocs, addDoc, updateDoc, deleteDoc, query, where, orderBy, limit, Timestamp
} from "https://www.gstatic.com/firebasejs/9.22.0/firebase-firestore.js";

// ==========================
// AUTHENTICATION FUNCTIONS
// ==========================

/**
 * Register a new user with email and password
 * @param {string} name - User's full name
 * @param {string} email - User's email
 * @param {string} password - User's password
 * @returns {Promise<Object>} User object or error
 */
export async function registerUser(name, email, password) {
  try {
    const userCredential = await createUserWithEmailAndPassword(auth, email, password);
    const user = userCredential.user;
    
    // Create user document in Firestore
    await createUserDocument(user.uid, name, email);
    
    return { success: true, user: user };
  } catch (error) {
    console.error("Error registering user:", error);
    return { success: false, error: error.message };
  }
}

/**
 * Sign in user with email and password
 * @param {string} email - User's email
 * @param {string} password - User's password
 * @returns {Promise<Object>} User object or error
 */
export async function loginUser(email, password) {
  try {
    const userCredential = await signInWithEmailAndPassword(auth, email, password);
    const user = userCredential.user;
    
    return { success: true, user: user };
  } catch (error) {
    console.error("Error logging in user:", error);
    return { success: false, error: error.message };
  }
}

/**
 * Sign out the current user
 * @returns {Promise<Object>} Success status or error
 */
export async function logoutUser() {
  try {
    await signOut(auth);
    return { success: true };
  } catch (error) {
    console.error("Error signing out user:", error);
    return { success: false, error: error.message };
  }
}

/**
 * Listen for authentication state changes
 * @param {Function} callback - Function to call when auth state changes
 */
export function onAuthStateChange(callback) {
  return onAuthStateChanged(auth, callback);
}

// ==========================
// USER DOCUMENT FUNCTIONS
// ==========================

/**
 * Create a user document in Firestore
 * @param {string} uid - Firebase user ID
 * @param {string} name - User's full name
 * @param {string} email - User's email
 */
async function createUserDocument(uid, name, email) {
  try {
    const userRef = doc(db, "users", uid);
    await setDoc(userRef, {
      uid: uid,
      name: name,
      email: email,
      createdAt: Timestamp.now(),
      preferences: []
    });
  } catch (error) {
    console.error("Error creating user document:", error);
  }
}

/**
 * Get user document from Firestore
 * @param {string} uid - Firebase user ID
 * @returns {Promise<Object>} User data or null
 */
export async function getUserDocument(uid) {
  try {
    const userRef = doc(db, "users", uid);
    const userSnap = await getDoc(userRef);
    
    if (userSnap.exists()) {
      return { success: true, data: userSnap.data() };
    } else {
      return { success: false, error: "User document not found" };
    }
  } catch (error) {
    console.error("Error getting user document:", error);
    return { success: false, error: error.message };
  }
}

/**
 * Update user preferences
 * @param {string} uid - Firebase user ID
 * @param {Array} preferences - Array of user preferences
 * @returns {Promise<Object>} Success status or error
 */
export async function updateUserPreferences(uid, preferences) {
  try {
    const userRef = doc(db, "users", uid);
    await updateDoc(userRef, {
      preferences: preferences
    });
    return { success: true };
  } catch (error) {
    console.error("Error updating user preferences:", error);
    return { success: false, error: error.message };
  }
}

// ==========================
// CHAT MESSAGE FUNCTIONS
// ==========================

/**
 * Send a chat message to Firestore
 * @param {string} userId - Firebase user ID
 * @param {string} userName - User's name
 * @param {string} messageText - Message content
 * @param {string} messageType - Type of message (user/bot)
 * @returns {Promise<Object>} Success status or error
 */
export async function sendChatMessage(userId, userName, messageText, messageType) {
  try {
    const messagesRef = collection(db, "messages");
    const messageData = {
      userId: userId,
      userName: userName,
      messageText: messageText,
      messageType: messageType,
      timestamp: Timestamp.now()
    };
    
    await addDoc(messagesRef, messageData);
    return { success: true };
  } catch (error) {
    console.error("Error sending chat message:", error);
    return { success: false, error: error.message };
  }
}

/**
 * Get chat messages from Firestore
 * @param {number} limitCount - Number of messages to retrieve
 * @returns {Promise<Array>} Array of messages or empty array
 */
export async function getChatMessages(limitCount = 50) {
  try {
    const messagesRef = collection(db, "messages");
    const q = query(messagesRef, orderBy("timestamp", "asc"), limit(limitCount));
    const querySnapshot = await getDocs(q);
    
    const messages = [];
    querySnapshot.forEach((doc) => {
      messages.push({ id: doc.id, ...doc.data() });
    });
    
    return { success: true, data: messages };
  } catch (error) {
    console.error("Error getting chat messages:", error);
    return { success: false, error: error.message };
  }
}

// ==========================
// DESTINATION FUNCTIONS
// ==========================

/**
 * Get travel destinations from Firestore
 * @returns {Promise<Array>} Array of destinations or empty array
 */
export async function getDestinations() {
  try {
    const destinationsRef = collection(db, "destinations");
    const querySnapshot = await getDocs(destinationsRef);
    
    const destinations = [];
    querySnapshot.forEach((doc) => {
      destinations.push({ id: doc.id, ...doc.data() });
    });
    
    return { success: true, data: destinations };
  } catch (error) {
    console.error("Error getting destinations:", error);
    return { success: false, error: error.message };
  }
}

/**
 * Get a specific destination by ID
 * @param {string} destinationId - Destination document ID
 * @returns {Promise<Object>} Destination data or null
 */
export async function getDestinationById(destinationId) {
  try {
    const destinationRef = doc(db, "destinations", destinationId);
    const destinationSnap = await getDoc(destinationRef);
    
    if (destinationSnap.exists()) {
      return { success: true, data: destinationSnap.data() };
    } else {
      return { success: false, error: "Destination not found" };
    }
  } catch (error) {
    console.error("Error getting destination:", error);
    return { success: false, error: error.message };
  }
}

// ==========================
// RECOMMENDATION FUNCTIONS
// ==========================

/**
 * Save AI recommendation to Firestore
 * @param {string} userId - Firebase user ID
 * @param {Object} recommendationData - Recommendation data
 * @returns {Promise<Object>} Success status or error
 */
export async function saveRecommendation(userId, recommendationData) {
  try {
    const recommendationsRef = collection(db, "recommendations");
    const recommendation = {
      userId: userId,
      ...recommendationData,
      timestamp: Timestamp.now()
    };
    
    await addDoc(recommendationsRef, recommendation);
    return { success: true };
  } catch (error) {
    console.error("Error saving recommendation:", error);
    return { success: false, error: error.message };
  }
}

/**
 * Get user recommendations from Firestore
 * @param {string} userId - Firebase user ID
 * @returns {Promise<Array>} Array of recommendations or empty array
 */
export async function getUserRecommendations(userId) {
  try {
    const recommendationsRef = collection(db, "recommendations");
    const q = query(recommendationsRef, where("userId", "==", userId), orderBy("timestamp", "desc"));
    const querySnapshot = await getDocs(q);
    
    const recommendations = [];
    querySnapshot.forEach((doc) => {
      recommendations.push({ id: doc.id, ...doc.data() });
    });
    
    return { success: true, data: recommendations };
  } catch (error) {
    console.error("Error getting user recommendations:", error);
    return { success: false, error: error.message };
  }
}