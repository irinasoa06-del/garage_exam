import { initializeApp } from "firebase/app";
import { getFirestore, collection, onSnapshot, addDoc, updateDoc, doc, deleteDoc } from "firebase/firestore";

// TODO: Remplacer par les vraies config de la console Firebase
const firebaseConfig = {
    apiKey: "AIzaSyBTlXXvAd7BmGmqy3NE2dcKbH6CiodTA7k",
    authDomain: "garage-exam.firebaseapp.com",
    projectId: "garage-exam",
    storageBucket: "garage-exam.firebasestorage.app",
    messagingSenderId: "945900310214",
    appId: "1:945900310214:web:e3de8cba06e7f62a95d1c7",
    measurementId: "G-5XRRNXTLBB"
};

const app = initializeApp(firebaseConfig);
const db = getFirestore(app);

// Collections references
const interventionsCol = collection(db, 'interventions');
const clientsCol = collection(db, 'clients');

export const firebaseService = {
    // Interventions (Types de réparation)
    getInterventions(callback) {
        return onSnapshot(interventionsCol, (snapshot) => {
            const data = snapshot.docs.map(doc => ({ id: doc.id, ...doc.data() }));
            callback(data);
        });
    },
    async addIntervention(intervention) {
        return await addDoc(interventionsCol, intervention);
    },
    async updateIntervention(id, intervention) {
        const docRef = doc(db, 'interventions', id);
        return await updateDoc(docRef, intervention);
    },
    async deleteIntervention(id) {
        const docRef = doc(db, 'interventions', id);
        return await deleteDoc(docRef);
    },

    // Clients (Réparations en cours)
    getClients(callback) {
        return onSnapshot(clientsCol, (snapshot) => {
            const data = snapshot.docs.map(doc => ({ id: doc.id, ...doc.data() }));
            callback(data);
        });
    }
};
