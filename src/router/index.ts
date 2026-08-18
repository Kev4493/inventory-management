import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import InventoryView from '@/views/InventoryView.vue'
import PlaygroundView from '@/views/PlaygroundView.vue'
import EmployeeView from '@/views/EmployeeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/inventory',
      name: 'inventory',
      component: InventoryView,
    },
    {
      path: '/employee',
      name: 'employee',
      component: EmployeeView,
    },
    {
      path: '/playground',
      name: 'playground',
      component: PlaygroundView,
    },
  ],
})

export default router
