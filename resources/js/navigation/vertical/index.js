export default [
  {
    title: 'tableau de bord',
    to: { name: 'root' },
    icon: { icon: 'tabler-home-2' },
  },
  {
    title: 'Poker Académie',
    icon: { icon: 'tabler-cube-plus' },
    children: [
      {
        title: 'Membres',
        to: { name: 'members' },
      },
      {
        title: 'Affiliations',
        to: '',
      },
      {
        title: 'Commandes',
        to: '',
      },
      {
        title: 'Tournois',
        to: '',
      },
    ],
  },
  {
    title: 'CMS',
    icon: { icon: 'tabler-device-desktop' },
    children: [
      {
        title: 'Articles',
        to: '',
      },
      {
        title: 'Tags',
        to: '',
      },
      {
        title: 'Tags PA',
        to: '',
      },
      {
        title: 'Modules',
        to: '',
      },
      {
        title: 'SEO',
        to: '',
      },
      {
        title: 'Menus',
        to: '',
      },
    ],
  },
  {
    title: 'Users',
    to: '',
    icon: { icon: 'tabler-users' },
  },
]
