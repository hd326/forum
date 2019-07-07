<script>

import Replies from '../components/Replies.vue';
import SubscribeButton from '../components/SubscribeButton.vue';

    export default {
        //props: ['dataRepliesCount', 'dataLocked'],
        props: ['thread'],
        components: { Replies, SubscribeButton },

        data () {
            return {
                //repliesCount: this.initialRepliesCount,
                //locked: this.dataLocked
                repliesCount: this.thread.replies_count,
                locked: this.thread.locked
            };
        },

        methods: {
            toggleLock () {
                axios.post[this.locked ? 'deleted' : 'post']('/locked-threads/' + this.thread.slug);
                
                this.locked = ! this.locked;
            }
        }
    }

</script>