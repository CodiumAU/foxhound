import { defineStore } from 'pinia'
import { http } from '../http'
import { ref } from 'vue'

export const useChannelsStore = defineStore('channels', () => {
  const channels = ref<ChannelListResource[]>([])
  const messages = ref<MessageListResource[]>([])
  const search = ref<string | null>(null)

  async function getChannels() {
    const response = await http.get<{ data: ChannelListResource[] }>(
      '/channels'
    )

    channels.value = response.data.data
  }

  async function getMessages(channel: string) {
    const response = await http.get<{ data: MessageListResource[] }>(
      `/channels/${channel}/messages`
    )

    messages.value = response.data.data
  }

  async function clearMessages(channel: string) {
    await http.delete(`/channels/${channel}/messages`)

    messages.value = []
  }

  return {
    search,
    messages,
    getMessages,
    clearMessages,
    channels,
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
  has_attachments: boolean
  subject: string
  recipients: {
    name: string | null
    address: string
  }[]
  sent_at: string
  data: Record<string, any>
}
