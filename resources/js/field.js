import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-list-box', IndexField)
  app.component('detail-list-box', DetailField)
  app.component('form-list-box', FormField)
})
