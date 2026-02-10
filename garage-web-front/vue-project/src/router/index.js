import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import ClientListView from '../views/Public/ClientListView.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'home',
            component: ClientListView
        },
        {
            path: '/login',
            name: 'login',
            component: LoginView
        },
        {
            path: '/client/:id/history',
            name: 'client-history',
            component: () => import('../views/Public/ClientHistoryView.vue')
        },
        {
            path: '/admin',
            name: 'admin',
            redirect: '/admin/dashboard',
            meta: { requiresAuth: true }, // Simple flag
            children: [
                {
                    path: 'dashboard',
                    name: 'dashboard',
                    component: () => import('../views/Admin/DashboardView.vue')
                },
                {
                    path: 'interventions',
                    name: 'interventions',
                    component: () => import('../views/Admin/InterventionsView.vue')
                }
            ]
        }
    ]
})

// Navigation Guard simple (Mock auth)
router.beforeEach((to, from, next) => {
    const isAuthenticated = !!localStorage.getItem('auth_token');

    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (!isAuthenticated) {
            next({ name: 'login' })
        } else {
            next()
        }
    } else {
        next() // make sure to always call next()!
    }
})

export default router
