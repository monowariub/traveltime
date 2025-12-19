// User Dashboard JavaScript for TravelTime AI
// This file handles all data interactions for the user dashboard using the Data Service

import {
  onAuthStateChange,
  getUserDocument,
  getChatMessages,
  getUserRecommendations,
  updateUserPreferences,
  saveUserAdmin, // Using this to update profile
  logoutUser
} from './data-service.js';

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
  onAuthStateChange(async (user) => {
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
    const userResult = await getUserDocument(currentUser.uid || currentUser.id);
    if (userResult.success) {
      currentUserData = userResult.data;
      userPreferences = currentUserData.preferences || [];
    }

    // Load additional data
    await Promise.all([
      loadChatMessagesData(),
      loadRecommendationsData()
    ]);
  } catch (error) {
    console.error("Error loading user data:", error);
  }
}

// Load chat messages
async function loadChatMessagesData() {
  // In a real app we'd filter by user ID, but our mock getAllMessages returns all or we need a specific filter
  // For now let's assume getChatMessages in data-service returns everything and we filter here or we improve data-service.
  // Actually data-service's getChatMessages(limit) just returns the last N messages.
  // Ideally we should update data-service to filter by user, but for now let's just use what we have and filter client side if needed,
  // or just accept we see all messages in this simple mock.
  // BUT, the original code filtered by userId.
  // Let's modify data service later if strictly needed, or just assume the mock is simple.
  // Wait, I can't modify data service easily without another write.
  // I entered "getChatMessages" which returns last 50.
  // Let's just use that.
  const result = await getChatMessages(50);
  if (result.success) {
    // Filter by user ID if possible, otherwise just show all for demo
    chatMessages = result.data.filter(m => m.userId === (currentUser.uid || currentUser.id));
  }
}

// Load recommendations
async function loadRecommendationsData() {
  const result = await getUserRecommendations(currentUser.uid || currentUser.id);
  if (result.success) {
    recommendations = result.data;
  }
}

// Update dashboard UI
function updateDashboardUI() {
  if (currentUser && currentUserData) {
    // Update user info
    const displayName = currentUserData.name || 'User';
    if (dashboardElements.userName) dashboardElements.userName.textContent = displayName;
    if (dashboardElements.welcomeUserName) dashboardElements.welcomeUserName.textContent = displayName.split(' ')[0];
    if (dashboardElements.userFullName) dashboardElements.userFullName.textContent = displayName;
    if (dashboardElements.userEmailDisplay) dashboardElements.userEmailDisplay.textContent = currentUser.email;
    if (dashboardElements.userAvatarLarge) dashboardElements.userAvatarLarge.textContent = displayName.charAt(0).toUpperCase();

    // Update profile form
    if (dashboardElements.profileName) dashboardElements.profileName.value = displayName;
    if (dashboardElements.profileEmail) dashboardElements.profileEmail.value = currentUser.email;

    if (dashboardElements.profileJoinDate && currentUserData.createdAt) {
      let joinDate;
      if (typeof currentUserData.createdAt === 'string') {
        joinDate = new Date(currentUserData.createdAt);
      } else if (currentUserData.createdAt.seconds) {
        joinDate = new Date(currentUserData.createdAt.seconds * 1000);
      }
      if (joinDate) dashboardElements.profileJoinDate.value = joinDate.toLocaleDateString();
    }

    // Update stats
    if (dashboardElements.preferencesCount) dashboardElements.preferencesCount.textContent = userPreferences.length;
    if (dashboardElements.chatMessagesCount) dashboardElements.chatMessagesCount.textContent = chatMessages.length;
    if (dashboardElements.recommendationsCount) dashboardElements.recommendationsCount.textContent = recommendations.length;

    // Update preferences display
    updatePreferencesDisplay();

    // Render tables
    renderChatHistoryTable();
    renderRecommendationsTable();
  }
}

// Update preferences display
function updatePreferencesDisplay() {
  if (!dashboardElements.preferencesContainer) return;

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
  if (!dashboardElements.chatHistoryTable) return;

  if (chatMessages.length === 0) {
    dashboardElements.chatHistoryTable.innerHTML = '<tr><td colspan="3" style="text-align: center;">No chat messages found</td></tr>';
    return;
  }

  dashboardElements.chatHistoryTable.innerHTML = chatMessages.map(message => {
    let dateStr = 'N/A';
    if (message.timestamp) {
      if (message.timestamp.seconds) dateStr = new Date(message.timestamp.seconds * 1000).toLocaleString();
      else dateStr = new Date(message.timestamp).toLocaleString();
    }

    return `
    <tr>
      <td>${message.messageText.substring(0, 50)}${message.messageText.length > 50 ? '...' : ''}</td>
      <td>${message.messageType === 'bot' ? 'Bot' : 'You'}</td>
      <td>${dateStr}</td>
    </tr>
  `}).join('');
}

// Render recommendations table
function renderRecommendationsTable() {
  if (!dashboardElements.recommendationsTable) return;

  if (recommendations.length === 0) {
    dashboardElements.recommendationsTable.innerHTML = '<tr><td colspan="4" style="text-align: center;">No recommendations found</td></tr>';
    return;
  }

  dashboardElements.recommendationsTable.innerHTML = recommendations.map(rec => {
    let dateStr = 'N/A';
    if (rec.timestamp) {
      if (rec.timestamp.seconds) dateStr = new Date(rec.timestamp.seconds * 1000).toLocaleDateString();
      else dateStr = new Date(rec.timestamp).toLocaleDateString();
    }

    return `
    <tr>
      <td>${rec.destination || 'Unknown'}</td>
      <td>${rec.reason ? rec.reason.substring(0, 50) + (rec.reason.length > 50 ? '...' : '') : 'N/A'}</td>
      <td>${dateStr}</td>
      <td>${rec.rating || 'N/A'}</td>
    </tr>
  `}).join('');
}

// Update user preferences handler
async function updateUserPreferencesHandler() {
  try {
    const result = await updateUserPreferences(currentUser.uid || currentUser.id, userPreferences);

    if (result.success) {
      showAlert(dashboardElements.preferencesAlert, 'Preferences saved successfully!', 'success');
      updateDashboardUI();
    } else {
      throw new Error(result.error);
    }
  } catch (error) {
    console.error("Error updating preferences:", error);
    showAlert(dashboardElements.preferencesAlert, 'Error saving preferences: ' + error.message, 'error');
  }
}

// Update user profile handler
async function updateUserProfileHandler(e) {
  e.preventDefault();

  try {
    const name = dashboardElements.profileName.value;
    const email = dashboardElements.profileEmail.value;

    const userData = {
      id: currentUser.uid || currentUser.id,
      name: name,
      email: email
    };

    const result = await saveUserAdmin(userData);

    if (result.success) {
      showAlert(dashboardElements.profileAlert, 'Profile updated successfully!', 'success');

      // Update local data
      if (currentUserData) {
        currentUserData.name = name;
        currentUserData.email = email;
      }

      updateDashboardUI();
    } else {
      throw new Error("Failed to update profile");
    }
  } catch (error) {
    console.error("Error updating profile:", error);
    showAlert(dashboardElements.profileAlert, 'Error updating profile: ' + error.message, 'error');
  }
}

// Show alert
function showAlert(alertElement, message, type) {
  if (!alertElement) return;
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
        await logoutUser();
        window.location.href = 'index.html';
      } catch (error) {
        console.error("Error logging out:", error);
        alert('Error logging out: ' + error.message);
      }
    }
  });
}

if (dashboardElements.profileForm) {
  dashboardElements.profileForm.addEventListener('submit', updateUserProfileHandler);
}

if (dashboardElements.preferenceBtns) {
  dashboardElements.preferenceBtns.forEach(btn => {
    btn.addEventListener('click', function () {
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
  dashboardElements.savePreferencesBtn.addEventListener('click', updateUserPreferencesHandler);
}

export {
  loadUserData,
  updateDashboardUI
};