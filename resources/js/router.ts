import { createRouter, createWebHistory } from 'vue-router'
import ChannelsMail from './pages/ChannelsMail.vue'
import ChannelsSms from './pages/ChannelsSms.vue'

export const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', redirect: '/channels/mail' },
    {
      path: '/channels/mail/:uuid?',
      name: 'channels.mail',
      props: true,
      component: ChannelsMail,
    },
    {
      path: '/channels/sms',
      name: 'channels.sms',
      component: ChannelsSms,
    },
  ],
})
