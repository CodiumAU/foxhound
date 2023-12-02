import { defineStore } from 'pinia'
import { http } from '../http'
import { ref } from 'vue'

export const useChannelsStore = defineStore('channels', () => {
  const channels = ref<ChannelListResource[]>([])
  const messages = ref<MessageListResource[]>([])

  async function getChannels() {
    const response = await http.get<{ data: ChannelListResource[] }>(
      '/channels'
    )

    channels.value = response.data.data
  }

  async function getMessages(channel: ChannelType) {
    const response = await http.get<{ data: MessageListResource[] }>(
      `/channels/${channel}/messages`
    )

    messages.value = response.data.data
  }

  return {
    messages,
    channels,
    getMessages,
    getChannels,
  }
})

export enum ChannelType {
  Mail = 'mail',
  Sms = 'sms',
}

export type ChannelListResource = {
  key: string
  name: string
  type: ChannelType
}

export type MessageListResource = {
  uuid: string
  unread: boolean
  subject: string | null
  recipients: {
    name: string | null
    address: string
  }[]
  sent_at: string
}
