<template>
  <div>
    <div v-for="(reply, index) in items" :key=reply.id>
      <reply :data="reply" @deleted="remove(index)"></reply>
    </div>
    <paginator :dataSet="dataSet" @updated="fetch"></paginator>
    <new-reply @created="add"></new-reply>
  </div>
</template>
<script>
import Reply from './Reply.vue'
import NewReply from './NewReply.vue'
export default {
 
  components: { Reply, NewReply },

  data() {
    return {
      dataSet: false,
      items: []      
    }
  },
  created(){
    this.fetch()
  },
  methods: {
    add(reply){
      this.items.push(reply);
      this.$emit('added')
    },
    remove(index){
      this.items.splice(index, 1);
      this.$emit('removed');
      flash('Reply was removed!')
    },
    fetch(page){
      axios.get(this.url(page))
          .then(this.refresh)
    },
    refresh({data}){
      this.dataSet = data;
      this.items = data.data;
      window.scrollTo(0, 0);
    },
    url(page){
      if(!page){
        let query = location.search.match(/page=(\d)/);
        page = query ? query[1] : 1;
      }
      return `${location.pathname}/replies?page=${page}`
    }
  }
}
</script>

  