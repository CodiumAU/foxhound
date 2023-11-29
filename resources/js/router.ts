import { createRouter, createWebHistory } from 'vue-router'
import ChannelsSingle from './pages/ChannelsSingle.vue'

export const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', redirect: '/channels/mail' },
    // {
    //   path: '/channels/mail/:uuid?',
    //   name: 'channels.mail',
    //   props: true,
    //   component: ChannelsMail,
    // },
    {
      path: '/channels/:channel/:uuid?',
      name: 'channels.single',
      props: true,
      component: ChannelsSingle,
    },
  ],
})
