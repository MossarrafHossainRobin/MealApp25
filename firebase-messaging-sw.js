importScripts('https://www.gstatic.com/firebasejs/9.6.10/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/9.6.10/firebase-messaging.js');

firebase.initializeApp({
  apiKey: "AIzaSyBTP4CkqCroMzj6M3Nu9VSypxbAgDSyAVM",
  authDomain: "localhost",
  projectId: "my-notify-app-3d1fc",
  storageBucket: "my-notify-app-3d1fc.appspot.com",
  messagingSenderId: "442485249076",
  appId: "1:442485249076:web:c6ab85eb0e475b43426661"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
  console.log('Received background message:', payload);
  
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: 'http://localhost/mealapp25/assets/img/logo.png',
    badge: 'http://localhost/mealapp25/assets/img/logo.png',
    tag: 'mealapp-notification'
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});

// Handle notification click
self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  event.waitUntil(
    clients.matchAll({ type: 'window' }).then((clientList) => {
      for (const client of clientList) {
        if (client.url.includes('localhost') && 'focus' in client) {
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow('http://localhost/mealapp25/water.php');
      }
    })
  );
});