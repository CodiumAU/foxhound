import { defineStore } from 'pinia'
import { http } from '../http'
import { ref } from 'vue'

export const useChannelsMailStore = defineStore('channelsMail', () => {
  const messages = ref<MessageListResource[]>([])

  async function getMailMessages() {
    const response = await http.get<{ data: MessageListResource[] }>(
      '/channels/mail'
    )

    messages.value = response.data.data
  }

  return {
    messages,
    getMailMessages,
  }
})

export type MessageListResource = {
  uuid: string
  unread: boolean
  subject: string
  recipients: {
    name: string | null
    address: string
  }[]
  sent_at: string
}
