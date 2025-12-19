// User Dashboard JavaScript for TravelTime AI
// This file handles all Firebase interactions for the user dashboard

import { auth, db } from './firebase-config.js';
import { 
  signOut, 
  onAuthStateChanged 
} from "https://www.gstatic.com/firebasejs/9.22.0/firebase-auth.js";
import { 
  collection, 
  doc, 
  getDoc, 
  getDocs, 
  updateDoc, 
  query, 
  where, 
  orderBy, 
  limit,
  Timestamp
} from "https://www.gstatic.com/firebasejs/9.22.0/firebase-firestore.js";

// Global variables
let currentUser = null;
let currentUserData = null;
let userPreferences = [];
let chatMessages = [];
let recommendations = [];

// DOM Elements
const dashboardElements = {
  userName: document.getElementById('userName'),
  welcomeUserName: document.getElementById('welcomeUserName'),
  userFullName: document.getElementById('userFullName'),
  userEmailDisplay: document.getElementById('userEmailDisplay'),
  userAvatarLarge: document.getElementById('userAvatarLarge'),
  preferencesCount: document.getElementById('preferencesCount'),
  chatMessagesCount: document.getElementById('chatMessagesCount'),
  recommendationsCount: document.getElementById('recommendationsCount'),
  preferencesContainer: document.getElementById('preferencesContainer'),
  profileName: document.getElementById('profileName'),
  profileEmail: document.getElementById('profileEmail'),
  profileJoinDate: document.getElementById('profileJoinDate'),
  profileLastLogin: document.getElementById('profileLastLogin'),
  chatHistoryTable: document.getElementById('chatHistoryTable'),
  recommendationsTable: document.getElementById('recommendationsTable'),
  logoutBtn: document.getElementById('logoutBtn'),
  profileForm: document.getElementById('profileForm'),
  passwordForm: document.getElementById('passwordForm'),
  preferenceBtns: document.querySelectorAll('.preference-btn'),
  selectedPreferences: document.getElementById('selectedPreferences'),
  savePreferencesBtn: document.getElementById('savePreferencesBtn'),
  profileAlert: document.getElementById('profileAlert'),
  preferencesAlert: document.getElementById('preferencesAlert'),
  chatAlert: document.getElementById('chatAlert'),
  recommendationsAlert: document.getElementById('recommendationsAlert')
};

// Initialize User Dashboard
export async function initializeUserDashboard() {
  // Check authentication state
  onAuthStateChanged(auth, async (user) => {
    if (user) {
      currentUser = user;
      // Load user data
      await loadUserData();
      updateDashboardUI();
    } else {
      // Redirect to login page
      window.location.href = 'index.html';
    }
  });
}

// Load user data
async function loadUserData() {
  try {
    const userDoc = await getDoc(doc(db, "users", currentUser.uid));
    if (userDoc.exists()) {
      currentUserData = userDoc.data();
      userPreferences = currentUserData.preferences || [];
    }
    
    // Load additional data
    await Promise.all([
      loadChatMessages(),
      loadRecommendations()
    ]);
  } catch (error) {
    console.error("Error loading user data:", error);
  }
}

// Load chat messages
async function loadChatMessages() {
  try {
    const messagesQuery = query(
      collection(db, "messages"),
      where("userId", "==", currentUser.uid),
      orderBy("timestamp", "desc"),
      limit(20)
    );
    
    const messagesSnapshot = await getDocs(messagesQuery);
    chatMessages = [];
    messagesSnapshot.forEach((doc) => {
      chatMessages.push({ id: doc.id, ...doc.data() });
    });
  } catch (error) {
    console.error("Error loading chat messages:", error);
  }
}

// Load recommendations
async function loadRecommendations() {
  try {
    const recommendationsQuery = query(
      collection(db, "recommendations"),
      where("userId", "==", currentUser.uid),
      orderBy("timestamp", "desc"),
      limit(20)
    );
    
    const recommendationsSnapshot = await getDocs(recommendationsQuery);
    recommendations = [];
    recommendationsSnapshot.forEach((doc) => {
      recommendations.push({ id: doc.id, ...doc.data() });
    });
  } catch (error) {
    console.error("Error loading recommendations:", error);
  }
}

// Update dashboard UI
function updateDashboardUI() {
  if (currentUser && currentUserData) {
    // Update user info
    const displayName = currentUserData.name || 'User';
    dashboardElements.userName.textContent = displayName;
    dashboardElements.welcomeUserName.textContent = displayName.split(' ')[0];
    dashboardElements.userFullName.textContent = displayName;
    dashboardElements.userEmailDisplay.textContent = currentUser.email;
    dashboardElements.userAvatarLarge.textContent = displayName.charAt(0).toUpperCase();
    
    // Update profile form
    dashboardElements.profileName.value = displayName;
    dashboardElements.profileEmail.value = currentUser.email;
    
    if (currentUserData.createdAt) {
      const joinDate = new Date(currentUserData.createdAt.seconds * 1000);
      dashboardElements.profileJoinDate.value = joinDate.toLocaleDateString();
    }
    
    // Update stats
    dashboardElements.preferencesCount.textContent = userPreferences.length;
    dashboardElements.chatMessagesCount.textContent = chatMessages.length;
    dashboardElements.recommendationsCount.textContent = recommendations.length;
    
    // Update preferences display
    updatePreferencesDisplay();
    
    // Render tables
    renderChatHistoryTable();
    renderRecommendationsTable();
  }
}

// Update preferences display
function updatePreferencesDisplay() {
  if (userPreferences.length === 0) {
    dashboardElements.preferencesContainer.innerHTML = '<div class="preference-tag">No preferences set</div>';
    return;
  }
  
  dashboardElements.preferencesContainer.innerHTML = userPreferences.map(pref => 
    `<div class="preference-tag">${pref.charAt(0).toUpperCase() + pref.slice(1)}</div>`
  ).join('');
}

// Render chat history table
function renderChatHistoryTable() {
  if (chatMessages.length === 0) {
    dashboardElements.chatHistoryTable.innerHTML = '<tr><td colspan="3" style="text-align: center;">No chat messages found</td></tr>';
    return;
  }
  
  dashboardElements.chatHistoryTable.innerHTML = chatMessages.map(message => `
    <tr>
      <td>${message.messageText.substring(0, 50)}${message.messageText.length > 50 ? '...' : ''}</td>
      <td>${message.messageType === 'bot' ? 'Bot' : 'You'}</td>
      <td>${message.timestamp ? new Date(message.timestamp.seconds * 1000).toLocaleString() : 'N/A'}</td>
    </tr>
  `).join('');
}

// Render recommendations table
function renderRecommendationsTable() {
  if (recommendations.length === 0) {
    dashboardElements.recommendationsTable.innerHTML = '<tr><td colspan="4" style="text-align: center;">No recommendations found</td></tr>';
    return;
  }
  
  dashboardElements.recommendationsTable.innerHTML = recommendations.map(rec => `
    <tr>
      <td>${rec.destination || 'Unknown'}</td>
      <td>${rec.reason ? rec.reason.substring(0, 50) + (rec.reason.length > 50 ? '...' : '') : 'N/A'}</td>
      <td>${rec.timestamp ? new Date(rec.timestamp.seconds * 1000).toLocaleDateString() : 'N/A'}</td>
      <td>${rec.rating || 'N/A'}</td>
    </tr>
  `).join('');
}

// Update user preferences
async function updateUserPreferences() {
  try {
    const userRef = doc(db, "users", currentUser.uid);
    await updateDoc(userRef, {
      preferences: userPreferences
    });
    
    showAlert(dashboardElements.preferencesAlert, 'Preferences saved successfully!', 'success');
    updateDashboardUI();
  } catch (error) {
    console.error("Error updating preferences:", error);
    showAlert(dashboardElements.preferencesAlert, 'Error saving preferences: ' + error.message, 'error');
  }
}

// Update user profile
async function updateUserProfile(e) {
  e.preventDefault();
  
  try {
    const name = dashboardElements.profileName.value;
    const email = dashboardElements.profileEmail.value;
    
    const userRef = doc(db, "users", currentUser.uid);
    await updateDoc(userRef, {
      name: name,
      email: email
    });
    
    showAlert(dashboardElements.profileAlert, 'Profile updated successfully!', 'success');
    
    // Update local data
    if (currentUserData) {
      currentUserData.name = name;
      currentUserData.email = email;
    }
    
    updateDashboardUI();
  } catch (error) {
    console.error("Error updating profile:", error);
    showAlert(dashboardElements.profileAlert, 'Error updating profile: ' + error.message, 'error');
  }
}

// Show alert
function showAlert(alertElement, message, type) {
  alertElement.textContent = message;
  alertElement.className = `alert show ${type}`;
  
  // Hide after 3 seconds
  setTimeout(() => {
    alertElement.classList.remove('show');
  }, 3000);
}

// Event Listeners
if (dashboardElements.logoutBtn) {
  dashboardElements.logoutBtn.addEventListener('click', async () => {
    if (confirm('Are you sure you want to logout?')) {
      try {
        await signOut(auth);
        window.location.href = 'index.html';
      } catch (error) {
        console.error("Error logging out:", error);
        alert('Error logging out: ' + error.message);
      }
    }
  });
}

if (dashboardElements.profileForm) {
  dashboardElements.profileForm.addEventListener('submit', updateUserProfile);
}

if (dashboardElements.preferenceBtns) {
  dashboardElements.preferenceBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      const pref = this.getAttribute('data-pref');
      
      if (userPreferences.includes(pref)) {
        // Remove preference
        userPreferences = userPreferences.filter(p => p !== pref);
        this.classList.remove('btn-primary');
        this.classList.add('btn-outline');
      } else {
        // Add preference
        userPreferences.push(pref);
        this.classList.remove('btn-outline');
        this.classList.add('btn-primary');
      }
      
      // Update displayed preferences
      updatePreferencesDisplay();
    });
  });
}

if (dashboardElements.savePreferencesBtn) {
  dashboardElements.savePreferencesBtn.addEventListener('click', updateUserPreferences);
}

// Initialize the user dashboard when the module is imported
// initializeUserDashboard();

export { 
  loadUserData, 
  loadChatMessages, 
  loadRecommendations,
  updateDashboardUI
};