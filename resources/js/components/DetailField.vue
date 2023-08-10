<template>
    <PanelItem :index="index" :field="field">
        <template #value>
            <div class="flex flex-wrap gap-2">
                <button v-for="item in lists" class="appearance-none inline-flex items-center text-left rounded-lg !cursor-default">
                    <span class="inline-flex items-center whitespace-nowrap min-h-6 px-2 rounded-full uppercase text-xs font-bold bg-primary-50 dark:bg-primary-500 text-primary-600 dark:text-gray-900 space-x-1">
                        <span>{{ item.name }}</span>
                    </span>
                </button>
            </div>
        </template>
    </PanelItem>
</template>

<script>
export default {
    props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

    data() {
        return {
            lists: [],
        }
    },

    methods: {
        loadLists() {
            let lists = this.field.value;
            Nova
                .request()
                .get(this.field.options)
                .then(response => {
                    let list = [];
                    for (let i = 0; i < response.data.resources.length; i++) {
                        let item = response.data.resources[i];
                        if (item.value === undefined && item.id === undefined) continue;
                        let code = item.value || item.id;
                        if (!lists.includes(code)) continue;
                        list.push(item);
                    }
                    this.formatLists(list);
                });
        },

        // 格式化数据
        formatLists(lists) {
            let list = [];
            let code;
            let name;
            for (let i = 0; i < lists.length; i++) {
                code = lists[i].value || lists[i].id
                name = lists[i].display || lists[i].name;
                list.push({
                    code: code,
                    name: (this.field.nameWithCode ? (code + ' - ') : '') + name
                });
            }
            this.lists = list;
        },
    },

    mounted() {
        let lists = this.field.value;
        if (typeof lists !== 'object' || lists.length === 0) return;
        if (this.field.belongsToMany) {         // BelongsToMany情况下，直接渲染列表
            this.formatLists(lists);
        } else {
            this.loadLists()
        }
    },
}
</script>
