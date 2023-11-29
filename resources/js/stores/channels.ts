import { defineStore } from 'pinia'
import { http } from '../http'
import { ref } from 'vue'

export const useChannelsStore = defineStore('channels', () => {
  const messages = ref<MessageListResource[]>([])

  async function getMessages(channel: string) {
    const response = await http.get<{ data: MessageListResource[] }>(
      `/channels/${channel}/messages`
    )

    messages.value = response.data.data
  }

  return {
    messages,
    getMessages,
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
