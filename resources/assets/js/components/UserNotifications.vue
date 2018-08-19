<template>
  <li class="dropdown" v-if="notifications.length">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
      Notifications
    </a>
    <ul class="dropdown-menu">
      <li v-for="notification in notifications">
        <a :href="notification.data.link" v-text="notification.data.message" @click="markAsRead(notification)">
         
        </a>
      </li>
    </ul>
  </li>
</template>
<script>

    export default {
      data(){
        return {
          notifications: false
        }
      },
      created(){
        axios.get(`/LC/tddforum/public/profiles/` + window.App.user.name + `/notifications`)
              .then(response => this.notifications = response.data)
      },
      methods: {
        markAsRead(notification){
          axios.delete(`/LC/tddforum/public/profiles/`+ window.App.user.name + `/notifications/` + notification.id)
        }
      }
    }
</script>
