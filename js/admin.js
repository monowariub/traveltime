// Admin Panel JavaScript for TravelTime AI
// This file handles all Firebase interactions for the admin panel

import { auth, db } from './firebase-config.js';
import { 
  signInWithEmailAndPassword, 
  signOut, 
  onAuthStateChanged 
} from "https://www.gstatic.com/firebasejs/9.22.0/firebase-auth.js";
import { 
  collection, 
  doc, 
  getDoc, 
  getDocs, 
  setDoc, 
  addDoc, 
  updateDoc, 
  deleteDoc, 
  query, 
  where, 
  orderBy, 
  limit,
  Timestamp
} from "https://www.gstatic.com/firebasejs/9.22.0/firebase-firestore.js";

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
  onAuthStateChanged(auth, async (user) => {
    if (user) {
      currentUser = user;
      
      // Check if user is admin
      const isAdmin = await checkAdminStatus(user.uid);
      if (isAdmin) {
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

// Check if user has admin privileges
async function checkAdminStatus(uid) {
  try {
    const userDoc = await getDoc(doc(db, "users", uid));
    if (userDoc.exists()) {
      const userData = userDoc.data();
      return userData.role === 'admin';
    }
    return false;
  } catch (error) {
    console.error("Error checking admin status:", error);
    return false;
  }
}

// Load all admin data
async function loadAdminData() {
  try {
    await Promise.all([
      loadUsers(),
      loadDestinations(),
      loadMessages(),
      loadRecommendations()
    ]);
    
    updateStats();
    renderUsersTable();
    renderDestinationsTable();
    renderMessagesTable();
    renderActivityLog();
  } catch (error) {
    console.error("Error loading admin data:", error);
  }
}

// Load users data
async function loadUsers() {
  try {
    const usersQuery = query(collection(db, "users"), orderBy("createdAt", "desc"));
    const usersSnapshot = await getDocs(usersQuery);
    
    usersData = [];
    usersSnapshot.forEach((doc) => {
      usersData.push({ id: doc.id, ...doc.data() });
    });
  } catch (error) {
    console.error("Error loading users:", error);
  }
}

// Load destinations data
async function loadDestinations() {
  try {
    const destinationsQuery = query(collection(db, "destinations"), orderBy("name"));
    const destinationsSnapshot = await getDocs(destinationsQuery);
    
    destinationsData = [];
    destinationsSnapshot.forEach((doc) => {
      destinationsData.push({ id: doc.id, ...doc.data() });
    });
  } catch (error) {
    console.error("Error loading destinations:", error);
  }
}

// Load messages data
async function loadMessages() {
  try {
    const messagesQuery = query(collection(db, "messages"), orderBy("timestamp", "desc"), limit(50));
    const messagesSnapshot = await getDocs(messagesQuery);
    
    messagesData = [];
    messagesSnapshot.forEach((doc) => {
      messagesData.push({ id: doc.id, ...doc.data() });
    });
  } catch (error) {
    console.error("Error loading messages:", error);
  }
}

// Load recommendations data
async function loadRecommendations() {
  try {
    const recommendationsQuery = query(collection(db, "recommendations"), orderBy("timestamp", "desc"));
    const recommendationsSnapshot = await getDocs(recommendationsQuery);
    
    recommendationsData = [];
    recommendationsSnapshot.forEach((doc) => {
      recommendationsData.push({ id: doc.id, ...doc.data() });
    });
  } catch (error) {
    console.error("Error loading recommendations:", error);
  }
}

// Update statistics
function updateStats() {
  adminElements.usersCount.textContent = usersData.length;
  adminElements.destinationsCount.textContent = destinationsData.length;
  adminElements.messagesCount.textContent = messagesData.length;
  adminElements.recommendationsCount.textContent = recommendationsData.length;
}

// Update admin UI
function updateAdminUI() {
  if (currentUser) {
    adminElements.adminName.textContent = "Admin";
  }
}

// Render users table
function renderUsersTable(searchTerm = '') {
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
  
  adminElements.usersTableBody.innerHTML = filteredUsers.map(user => `
    <tr>
      <td>${user.name}</td>
      <td>${user.email}</td>
      <td>${user.createdAt ? new Date(user.createdAt.seconds * 1000).toLocaleDateString() : 'N/A'}</td>
      <td><span class="badge ${user.role === 'admin' ? 'success' : 'warning'}">${user.role || 'user'}</span></td>
      <td class="action-buttons">
        <button class="action-btn edit" data-id="${user.id}"><i class="fas fa-edit"></i></button>
        <button class="action-btn delete" data-id="${user.id}"><i class="fas fa-trash"></i></button>
      </td>
    </tr>
  `).join('');
  
  // Add event listeners to action buttons
  document.querySelectorAll('#usersTableBody .action-btn.edit').forEach(btn => {
    btn.addEventListener('click', (e) => editUser(e.currentTarget.dataset.id));
  });
  
  document.querySelectorAll('#usersTableBody .action-btn.delete').forEach(btn => {
    btn.addEventListener('click', (e) => deleteUser(e.currentTarget.dataset.id));
  });
}

// Render destinations table
function renderDestinationsTable(searchTerm = '') {
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
    btn.addEventListener('click', (e) => deleteDestination(e.currentTarget.dataset.id));
  });
}

// Render messages table
function renderMessagesTable(searchTerm = '') {
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
  
  adminElements.messagesTableBody.innerHTML = filteredMessages.map(message => `
    <tr>
      <td>${message.userName || 'Anonymous'}</td>
      <td>${message.messageText.substring(0, 50)}${message.messageText.length > 50 ? '...' : ''}</td>
      <td>${message.messageType === 'bot' ? 'Bot' : 'User'}</td>
      <td>${message.timestamp ? new Date(message.timestamp.seconds * 1000).toLocaleString() : 'N/A'}</td>
      <td class="action-buttons">
        <button class="action-btn delete" data-id="${message.id}"><i class="fas fa-trash"></i></button>
      </td>
    </tr>
  `).join('');
  
  // Add event listeners to action buttons
  document.querySelectorAll('#messagesTableBody .action-btn.delete').forEach(btn => {
    btn.addEventListener('click', (e) => deleteMessage(e.currentTarget.dataset.id));
  });
}

// Render activity log
function renderActivityLog() {
  // For now, we'll show a simple static log
  // In a real implementation, this would show actual admin activities
  adminElements.activityTableBody.innerHTML = `
    <tr>
      <td>System</td>
      <td>Loaded dashboard</td>
      <td>Admin Panel</td>
      <td>${new Date().toLocaleTimeString()}</td>
    </tr>
    <tr>
      <td>System</td>
      <td>Retrieved user data</td>
      <td>Users Collection</td>
      <td>${new Date(Date.now() - 60000).toLocaleTimeString()}</td>
    </tr>
  `;
}

// Edit user
function editUser(userId) {
  const user = usersData.find(u => u.id === userId);
  if (user) {
    document.getElementById('userModalTitle').textContent = 'Edit User';
    document.getElementById('userId').value = user.id;
    document.getElementById('userName').value = user.name || '';
    document.getElementById('userEmail').value = user.email || '';
    document.getElementById('userRole').value = user.role || 'user';
    
    adminElements.userModal.classList.add('show');
  }
}

// Delete user
async function deleteUser(userId) {
  if (confirm('Are you sure you want to delete this user?')) {
    try {
      await deleteDoc(doc(db, "users", userId));
      showAlert(adminElements.usersAlert, 'User deleted successfully!', 'success');
      await loadUsers();
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

// Delete destination
async function deleteDestination(destinationId) {
  if (confirm('Are you sure you want to delete this destination?')) {
    try {
      await deleteDoc(doc(db, "destinations", destinationId));
      showAlert(adminElements.destinationsAlert, 'Destination deleted successfully!', 'success');
      await loadDestinations();
      renderDestinationsTable();
      updateStats();
    } catch (error) {
      console.error("Error deleting destination:", error);
      showAlert(adminElements.destinationsAlert, 'Error deleting destination: ' + error.message, 'error');
    }
  }
}

// Delete message
async function deleteMessage(messageId) {
  if (confirm('Are you sure you want to delete this message?')) {
    try {
      await deleteDoc(doc(db, "messages", messageId));
      showAlert(adminElements.messagesAlert, 'Message deleted successfully!', 'success');
      await loadMessages();
      renderMessagesTable();
      updateStats();
    } catch (error) {
      console.error("Error deleting message:", error);
      showAlert(adminElements.messagesAlert, 'Error deleting message: ' + error.message, 'error');
    }
  }
}

// Save user
async function saveUser() {
  const userId = document.getElementById('userId').value;
  const name = document.getElementById('userName').value;
  const email = document.getElementById('userEmail').value;
  const password = document.getElementById('userPassword').value;
  const role = document.getElementById('userRole').value;
  
  try {
    if (userId) {
      // Update existing user
      const userRef = doc(db, "users", userId);
      const updateData = {
        name: name,
        email: email,
        role: role
      };
      
      // Only update password if provided
      if (password) {
        updateData.password = password;
      }
      
      await updateDoc(userRef, updateData);
      showAlert(adminElements.usersAlert, 'User updated successfully!', 'success');
    } else {
      // Create new user
      const newUser = {
        name: name,
        email: email,
        role: role,
        createdAt: Timestamp.now()
      };
      
      // Add password if provided
      if (password) {
        newUser.password = password;
      }
      
      await addDoc(collection(db, "users"), newUser);
      showAlert(adminElements.usersAlert, 'User created successfully!', 'success');
    }
    
    adminElements.userModal.classList.remove('show');
    await loadUsers();
    renderUsersTable();
    updateStats();
  } catch (error) {
    console.error("Error saving user:", error);
    showAlert(adminElements.usersAlert, 'Error saving user: ' + error.message, 'error');
  }
}

// Save destination
async function saveDestination() {
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
        location: "Bangladesh", // Default for this app
        type: travelerType
      }
    };
    
    if (destinationId) {
      // Update existing destination
      const destinationRef = doc(db, "destinations", destinationId);
      await updateDoc(destinationRef, destinationData);
      showAlert(adminElements.destinationsAlert, 'Destination updated successfully!', 'success');
    } else {
      // Create new destination
      destinationData.createdAt = Timestamp.now();
      await addDoc(collection(db, "destinations"), destinationData);
      showAlert(adminElements.destinationsAlert, 'Destination created successfully!', 'success');
    }
    
    adminElements.destinationModal.classList.remove('show');
    await loadDestinations();
    renderDestinationsTable();
    updateStats();
  } catch (error) {
    console.error("Error saving destination:", error);
    showAlert(adminElements.destinationsAlert, 'Error saving destination: ' + error.message, 'error');
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
if (adminElements.logoutBtn) {
  adminElements.logoutBtn.addEventListener('click', async () => {
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
  adminElements.saveUserBtn.addEventListener('click', saveUser);
}

if (adminElements.saveDestinationBtn) {
  adminElements.saveDestinationBtn.addEventListener('click', saveDestination);
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

// Initialize the admin panel when the module is imported
// initializeAdminPanel();

export { 
  loadUsers, 
  loadDestinations, 
  loadMessages, 
  loadRecommendations,
  renderUsersTable,
  renderDestinationsTable,
  renderMessagesTable
};