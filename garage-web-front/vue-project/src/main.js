import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

import './assets/styles.css' // Modern Design System
import '@fortawesome/fontawesome-free/css/all.css' // FontAwesome Icons

const app = createApp(App)

app.use(router)

app.mount('#app')
