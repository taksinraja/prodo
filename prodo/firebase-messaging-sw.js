// firebase-messaging-sw.js
importScripts('https://www.gstatic.com/firebasejs/11.0.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/11.0.1/firebase-messaging.js');

// Initialize Firebase with the same config used in your main script
const firebaseConfig = {
    apiKey: "AIzaSyApjEP6dlcHlITUWz6-PtPj3qvtwY1NLIE",
    authDomain: "prodo-notification.firebaseapp.com",
    projectId: "prodo-notification",
    storageBucket: "prodo-notification.appspot.com",
    messagingSenderId: "756302964220",
    appId: "1:756302964220:web:840a3bd367bc80914e7e79",
    measurementId: "G-NXXEL2672V"
};

firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    console.log('Received background message: ', payload);
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: payload.notification.icon
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});