// Admin Panel JavaScript for TravelTime AI
// This file handles all data interactions for the admin panel using the Data Service

import {
  onAuthStateChange,
  loadUsers,
  saveUserAdmin,
  deleteUser,
  getDestinations,
  saveDestination,
  deleteDestination,
  getAllMessages,
  deleteMessage,
  getAllRecommendations,
  logoutUser
} from './data-service.js';

// Global variables
let currentUser = null;
let usersData = [];
let destinationsData = [];
let messagesData = [];
let recommendationsData = [];

// DOM Elements
const adminElements = {
  adminName: document.getElementById('adminName'),
  logoutBtn: document.getElementById('logoutBtn'),
  usersCount: document.getElementById('usersCount'),
  destinationsCount: document.getElementById('destinationsCount'),
  messagesCount: document.getElementById('messagesCount'),
  recommendationsCount: document.getElementById('recommendationsCount'),
  usersTableBody: document.getElementById('usersTableBody'),
  destinationsTableBody: document.getElementById('destinationsTableBody'),
  messagesTableBody: document.getElementById('messagesTableBody'),
  activityTableBody: document.getElementById('activityTableBody'),
  userSearch: document.getElementById('userSearch'),
  destinationSearch: document.getElementById('destinationSearch'),
  messageSearch: document.getElementById('messageSearch'),
  addUserBtn: document.getElementById('addUserBtn'),
  addDestinationBtn: document.getElementById('addDestinationBtn'),
  userModal: document.getElementById('userModal'),
  destinationModal: document.getElementById('destinationModal'),
  userForm: document.getElementById('userForm'),
  destinationForm: document.getElementById('destinationForm'),
  saveUserBtn: document.getElementById('saveUserBtn'),
  saveDestinationBtn: document.getElementById('saveDestinationBtn'),
  usersAlert: document.getElementById('usersAlert'),
  destinationsAlert: document.getElementById('destinationsAlert'),
  messagesAlert: document.getElementById('messagesAlert')
};

// Initialize Admin Panel
export async function initializeAdminPanel() {
  // Check authentication state
  onAuthStateChange(async (user) => {
    if (user) {
      currentUser = user;

      // Check if user is admin
      if (user.role === 'admin') {
        // Load admin data
        await loadAdminData();
        updateAdminUI();
      } else {
        // Redirect to regular user page or show error
        alert('Access denied. Admin privileges required.');
        window.location.href = 'index.html';
      }
    } else {
      // Redirect to login page
      window.location.href = 'index.html';
    }
  });
}

// Load all admin data
async function loadAdminData() {
  try {
    const [users, destinations, messages, recommendations] = await Promise.all([
      loadUsers(),
      getDestinations(),
      getAllMessages(),
      getAllRecommendations()
    ]);

    usersData = users;
    destinationsData = destinations;
    messagesData = messages;
    recommendationsData = recommendations;

    updateStats();
    renderUsersTable();
    renderDestinationsTable();
    renderMessagesTable();
    renderActivityLog();
  } catch (error) {
    console.error("Error loading admin data:", error);
  }
}

// Update statistics
function updateStats() {
  if (adminElements.usersCount) adminElements.usersCount.textContent = usersData.length;
  if (adminElements.destinationsCount) adminElements.destinationsCount.textContent = destinationsData.length;
  if (adminElements.messagesCount) adminElements.messagesCount.textContent = messagesData.length;
  if (adminElements.recommendationsCount) adminElements.recommendationsCount.textContent = recommendationsData.length;
}

// Update admin UI
function updateAdminUI() {
  if (currentUser && adminElements.adminName) {
    adminElements.adminName.textContent = currentUser.name || "Admin";
  }
}

// Render users table
export function renderUsersTable(searchTerm = '') {
  if (!adminElements.usersTableBody) return;

  let filteredUsers = usersData;

  if (searchTerm) {
    filteredUsers = usersData.filter(user =>
      user.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      user.email.toLowerCase().includes(searchTerm.toLowerCase())
    );
  }

  if (filteredUsers.length === 0) {
    adminElements.usersTableBody.innerHTML = '<tr><td colspan="5" style="text-align: center;">No users found</td></tr>';
    return;
  }

  adminElements.usersTableBody.innerHTML = filteredUsers.map(user => {
    let dateStr = 'N/A';
    if (user.createdAt) {
      if (typeof user.createdAt === 'string') dateStr = new Date(user.createdAt).toLocaleDateString();
      else if (user.createdAt.seconds) dateStr = new Date(user.createdAt.seconds * 1000).toLocaleDateString();
    }

    return `
    <tr>
      <td>${user.name}</td>
      <td>${user.email}</td>
      <td>${dateStr}</td>
      <td><span class="badge ${user.role === 'admin' ? 'success' : 'warning'}">${user.role || 'user'}</span></td>
      <td class="action-buttons">
        <button class="action-btn edit" data-id="${user.uid || user.id}"><i class="fas fa-edit"></i></button>
        <button class="action-btn delete" data-id="${user.uid || user.id}"><i class="fas fa-trash"></i></button>
      </td>
    </tr>
  `}).join('');

  // Add event listeners to action buttons
  document.querySelectorAll('#usersTableBody .action-btn.edit').forEach(btn => {
    btn.addEventListener('click', (e) => editUser(e.currentTarget.dataset.id));
  });

  document.querySelectorAll('#usersTableBody .action-btn.delete').forEach(btn => {
    btn.addEventListener('click', (e) => deleteUserHandler(e.currentTarget.dataset.id));
  });
}

// Render destinations table
export function renderDestinationsTable(searchTerm = '') {
  if (!adminElements.destinationsTableBody) return;

  let filteredDestinations = destinationsData;

  if (searchTerm) {
    filteredDestinations = destinationsData.filter(destination =>
      destination.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      (destination.description && destination.description.toLowerCase().includes(searchTerm.toLowerCase()))
    );
  }

  if (filteredDestinations.length === 0) {
    adminElements.destinationsTableBody.innerHTML = '<tr><td colspan="6" style="text-align: center;">No destinations found</td></tr>';
    return;
  }

  adminElements.destinationsTableBody.innerHTML = filteredDestinations.map(destination => `
    <tr>
      <td>${destination.name}</td>
      <td>${destination.description.substring(0, 50)}${destination.description.length > 50 ? '...' : ''}</td>
      <td>${destination.bestTime || 'N/A'}</td>
      <td>${destination.travelerType || 'N/A'}</td>
      <td>${destination.metadata && destination.metadata.rating ? destination.metadata.rating : 'N/A'}</td>
      <td class="action-buttons">
        <button class="action-btn edit" data-id="${destination.id}"><i class="fas fa-edit"></i></button>
        <button class="action-btn delete" data-id="${destination.id}"><i class="fas fa-trash"></i></button>
      </td>
    </tr>
  `).join('');

  // Add event listeners to action buttons
  document.querySelectorAll('#destinationsTableBody .action-btn.edit').forEach(btn => {
    btn.addEventListener('click', (e) => editDestination(e.currentTarget.dataset.id));
  });

  document.querySelectorAll('#destinationsTableBody .action-btn.delete').forEach(btn => {
    btn.addEventListener('click', (e) => deleteDestinationHandler(e.currentTarget.dataset.id));
  });
}

// Render messages table
export function renderMessagesTable(searchTerm = '') {
  if (!adminElements.messagesTableBody) return;

  let filteredMessages = messagesData;

  if (searchTerm) {
    filteredMessages = messagesData.filter(message =>
      (message.userName && message.userName.toLowerCase().includes(searchTerm.toLowerCase())) ||
      (message.messageText && message.messageText.toLowerCase().includes(searchTerm.toLowerCase()))
    );
  }

  if (filteredMessages.length === 0) {
    adminElements.messagesTableBody.innerHTML = '<tr><td colspan="5" style="text-align: center;">No messages found</td></tr>';
    return;
  }

  adminElements.messagesTableBody.innerHTML = filteredMessages.map(message => {
    let dateStr = 'N/A';
    if (message.timestamp) {
      if (message.timestamp.seconds) dateStr = new Date(message.timestamp.seconds * 1000).toLocaleString();
      else dateStr = new Date(message.timestamp).toLocaleString();
    }

    return `
    <tr>
      <td>${message.userName || 'Anonymous'}</td>
      <td>${message.messageText.substring(0, 50)}${message.messageText.length > 50 ? '...' : ''}</td>
      <td>${message.messageType === 'bot' ? 'Bot' : 'User'}</td>
      <td>${dateStr}</td>
      <td class="action-buttons">
        <button class="action-btn delete" data-id="${message.id}"><i class="fas fa-trash"></i></button>
      </td>
    </tr>
  `}).join('');

  // Add event listeners to action buttons
  document.querySelectorAll('#messagesTableBody .action-btn.delete').forEach(btn => {
    btn.addEventListener('click', (e) => deleteMessageHandler(e.currentTarget.dataset.id));
  });
}

// Render activity log
function renderActivityLog() {
  if (!adminElements.activityTableBody) return;
  // For now, we'll show a simple static log
  adminElements.activityTableBody.innerHTML = `
    <tr>
      <td>System</td>
      <td>Loaded dashboard</td>
      <td>Admin Panel</td>
      <td>${new Date().toLocaleTimeString()}</td>
    </tr>
    <tr>
      <td>System</td>
      <td>Mock data initialized</td>
      <td>LocalStorage</td>
      <td>${new Date().toLocaleTimeString()}</td>
    </tr>
  `;
}

// Edit user
function editUser(userId) {
  const user = usersData.find(u => u.uid === userId || u.id === userId);
  if (user) {
    document.getElementById('userModalTitle').textContent = 'Edit User';
    document.getElementById('userId').value = user.uid || user.id;
    document.getElementById('userName').value = user.name || '';
    document.getElementById('userEmail').value = user.email || '';
    document.getElementById('userRole').value = user.role || 'user';

    adminElements.userModal.classList.add('show');
  }
}

// Delete user handler
async function deleteUserHandler(userId) {
  if (confirm('Are you sure you want to delete this user?')) {
    try {
      await deleteUser(userId);
      showAlert(adminElements.usersAlert, 'User deleted successfully!', 'success');
      // Refresh local data
      usersData = await loadUsers();
      renderUsersTable();
      updateStats();
    } catch (error) {
      console.error("Error deleting user:", error);
      showAlert(adminElements.usersAlert, 'Error deleting user: ' + error.message, 'error');
    }
  }
}

// Edit destination
function editDestination(destinationId) {
  const destination = destinationsData.find(d => d.id === destinationId);
  if (destination) {
    document.getElementById('destinationModalTitle').textContent = 'Edit Destination';
    document.getElementById('destinationId').value = destination.id;
    document.getElementById('destinationName').value = destination.name || '';
    document.getElementById('destinationDescription').value = destination.description || '';
    document.getElementById('destinationImageUrl').value = destination.imageUrl || '';
    document.getElementById('destinationBestTime').value = destination.bestTime || '';
    document.getElementById('destinationType').value = destination.travelerType || '';

    if (destination.metadata && destination.metadata.rating) {
      document.getElementById('destinationRating').value = destination.metadata.rating;
    }

    adminElements.destinationModal.classList.add('show');
  }
}

// Delete destination handler
async function deleteDestinationHandler(destinationId) {
  if (confirm('Are you sure you want to delete this destination?')) {
    try {
      await deleteDestination(destinationId);
      showAlert(adminElements.destinationsAlert, 'Destination deleted successfully!', 'success');
      // Refresh local data
      const result = await getDestinations();
      destinationsData = result.data;
      renderDestinationsTable();
      updateStats();
    } catch (error) {
      console.error("Error deleting destination:", error);
      showAlert(adminElements.destinationsAlert, 'Error deleting destination: ' + error.message, 'error');
    }
  }
}

// Delete message handler
async function deleteMessageHandler(messageId) {
  if (confirm('Are you sure you want to delete this message?')) {
    try {
      await deleteMessage(messageId);
      showAlert(adminElements.messagesAlert, 'Message deleted successfully!', 'success');
      // Refresh local data
      messagesData = await getAllMessages();
      renderMessagesTable();
      updateStats();
    } catch (error) {
      console.error("Error deleting message:", error);
      showAlert(adminElements.messagesAlert, 'Error deleting message: ' + error.message, 'error');
    }
  }
}

// Save user handler
async function saveUserHandler() {
  const userId = document.getElementById('userId').value;
  const name = document.getElementById('userName').value;
  const email = document.getElementById('userEmail').value;
  const password = document.getElementById('userPassword').value;
  const role = document.getElementById('userRole').value;

  try {
    const userData = {
      id: userId,
      name,
      email,
      role
    };
    if (password) userData.password = password;

    await saveUserAdmin(userData);
    showAlert(adminElements.usersAlert, 'User saved successfully!', 'success');

    adminElements.userModal.classList.remove('show');
    usersData = await loadUsers();
    renderUsersTable();
    updateStats();
  } catch (error) {
    console.error("Error saving user:", error);
    showAlert(adminElements.usersAlert, 'Error saving user: ' + error.message, 'error');
  }
}

// Save destination handler
async function saveDestinationHandler() {
  const destinationId = document.getElementById('destinationId').value;
  const name = document.getElementById('destinationName').value;
  const description = document.getElementById('destinationDescription').value;
  const imageUrl = document.getElementById('destinationImageUrl').value;
  const bestTime = document.getElementById('destinationBestTime').value;
  const travelerType = document.getElementById('destinationType').value;
  const rating = parseFloat(document.getElementById('destinationRating').value) || null;

  try {
    const destinationData = {
      name: name,
      description: description,
      imageUrl: imageUrl,
      bestTime: bestTime,
      travelerType: travelerType,
      metadata: {
        rating: rating,
        location: "Bangladesh",
        type: travelerType
      }
    };

    if (destinationId) destinationData.id = destinationId;

    await saveDestination(destinationData);
    showAlert(adminElements.destinationsAlert, 'Destination saved successfully!', 'success');

    adminElements.destinationModal.classList.remove('show');
    const result = await getDestinations();
    destinationsData = result.data;
    renderDestinationsTable();
    updateStats();
  } catch (error) {
    console.error("Error saving destination:", error);
    showAlert(adminElements.destinationsAlert, 'Error saving destination: ' + error.message, 'error');
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
if (adminElements.logoutBtn) {
  adminElements.logoutBtn.addEventListener('click', async () => {
    if (confirm('Are you sure you want to logout?')) {
      try {
        await logoutUser();
        window.location.href = 'index.html';
      } catch (error) {
        alert('Error logging out: ' + error.message);
      }
    }
  });
}

if (adminElements.addUserBtn) {
  adminElements.addUserBtn.addEventListener('click', () => {
    document.getElementById('userModalTitle').textContent = 'Add User';
    adminElements.userForm.reset();
    document.getElementById('userId').value = '';
    adminElements.userModal.classList.add('show');
  });
}

if (adminElements.addDestinationBtn) {
  adminElements.addDestinationBtn.addEventListener('click', () => {
    document.getElementById('destinationModalTitle').textContent = 'Add Destination';
    adminElements.destinationForm.reset();
    document.getElementById('destinationId').value = '';
    adminElements.destinationModal.classList.add('show');
  });
}

if (adminElements.saveUserBtn) {
  adminElements.saveUserBtn.addEventListener('click', saveUserHandler);
}

if (adminElements.saveDestinationBtn) {
  adminElements.saveDestinationBtn.addEventListener('click', saveDestinationHandler);
}

if (adminElements.userSearch) {
  adminElements.userSearch.addEventListener('input', (e) => {
    renderUsersTable(e.target.value);
  });
}

if (adminElements.destinationSearch) {
  adminElements.destinationSearch.addEventListener('input', (e) => {
    renderDestinationsTable(e.target.value);
  });
}

if (adminElements.messageSearch) {
  adminElements.messageSearch.addEventListener('input', (e) => {
    renderMessagesTable(e.target.value);
  });
}

export { loadUsers };