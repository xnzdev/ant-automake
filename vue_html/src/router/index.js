import Vue from 'vue'
import Router from 'vue-router'
// import HelloWorld from '@/components/HelloWorld'
import Builder from '@/components/builder/Builder'
import Config from '@/components/builder/Config'
import Format from '@/components/format/Format'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'Builder',
      component: Builder
    },
    {
      path: '/config',
      name: 'Config',
      component: Config
    },
    {
      path: '/format',
      name: 'Format',
      component: Format
    }
  ]
})
