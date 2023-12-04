import {
  default as ModalDialog,
  type Props,
} from '../components/common/ModalDialog.vue'
import { createApp } from 'vue'

export function useAlert(options: Omit<Props, 'onResponse'>): Promise<boolean> {
  return new Promise((resolve) => {
    const element = document.createElement('div')
    document.body.appendChild(element)

    const alert = createApp(ModalDialog, {
      ...options,
      onResponse(confirmed: boolean) {
        resolve(confirmed)
      },
      onClosed() {
        element.parentNode!.removeChild(element!)
        alert.unmount()
      },
    })

    alert.mount(element)
  })
}
