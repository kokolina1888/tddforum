<script>
import Replies from '../components/Replies.vue'
import SubscribeButton from '../components/SubscribeButton.vue'
	export default {
		props: ['initialRepliesCount'],
		component: { Replies },
		data() {
			return {
				repliesCount: this.initialRepliesCount
			}
		}

	}
</script>