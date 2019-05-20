<template>
    <div>
        <div v-for="(reply, index) in items" :key="reply.id">
            <reply :data="reply" @deleted="remove(index)" v-cloak></reply>
        </div>

        <paginator :dataSet="dataSet" @changed="fetch"></paginator>

        <new-reply @created="add"></new-reply>
        <!--:endpoint="endpoint"-->
    </div>
</template>
<script>
    import NewReply from './NewReply.vue';
    import Reply from './Reply.vue';

    export default {
        //props: ['data'],
        components: {
            Reply,
            NewReply
        },
        // replies takes in $thread->reply
        // reply takes in data from replies data object
        // 
        data() {
            return {
                //items: this.data,
                dataSet: false,
                items: [],
                //endpoint: location.pathname + '/replies'
            }
        },

        created() {
            this.fetch();
        },

        methods: {
            fetch(page) {
                axios.get(this.url(page))
                    .then(this.refresh);
            },

            url(page) {
                if (! page) {
                    let query = location.search.match(/page=(\d+)/);
                    page = query ? query[1] : 1;
                }
                return `${location.pathname}/replies?page=${page}`; //+ page;
            },

            refresh({data}) {
                this.dataSet = data;
                this.items = data.data;
                //console.log(data);
                window.scrollTo(0, 0);
                //episode 39
            },

            add(reply) {
                this.items.push(reply);
                this.$emit('added');
            },

            remove(index) {
                this.items.splice(index, 1);
                this.$emit('removed');
                flash('Reply was deleted');
            }
        },
    }
</script>