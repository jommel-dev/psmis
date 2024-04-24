
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
      { path: 'usermanagement', name: 'usermanagement', component: () => import('pages/UserManage.vue') },
      { path: 'auction/list', name: 'auctionlist', component: () => import('pages/Auctioned.vue') },
      { path: 'loans/list', name: 'loanslist', component: () => import('pages/Loans.vue') },
      { path: 'sales/list', name: 'saleslist', component: () => import('pages/UserManage.vue') },
      { path: 'reports', name: 'reports', component: () => import('pages/Reports.vue') },
      { path: 'configuration', name: 'configuration', component: () => import('pages/Configurations.vue') },
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
      { 
        path: 'auction',
        name: 'auctionList',
        component: () => import('pages/Users/Auctions.vue') 
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
