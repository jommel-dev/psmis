
const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/Index.vue') }
    ]
  },

  {
    path: '/',
    component: () => import('layouts/LoginLayout.vue'),
    children: [
      { path: 'dashboard', name: 'dashboard', component: () => import('pages/Dashboard.vue') },
      { path: 'forms', name: 'forms', component: () => import('pages/ORForm.vue') },
      { path: 'print', name: 'print', component: () => import('pages/Print.vue') },
      { path: 'mylist', name: 'mylist', component: () => import('pages/PatientList.vue') },
      { path: 'mysavelist', name: 'mysavelist', component: () => import('pages/SaveList.vue') },
      { path: 'usermanagement', name: 'usermanagement', component: () => import('pages/UserManage.vue') },
      { path: 'crsmanagement', name: 'crsmanagement', component: () => import('pages/Philhealth.vue') },
    ]
  },
  
  {
    path: '/user/',
    component: () => import('layouts/UsersLayout.vue'),
    children: [
      { 
        path: 'dashboard',
        name: 'userDashboard',
        component: () => import('pages/Users/Dashboard.vue') 
      },
      { 
        path: 'applications',
        name: 'userApplications',
        component: () => import('pages/Users/Applications.vue') 
      },
    ]
  },
  

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/Error404.vue')
  }
]

export default routes
