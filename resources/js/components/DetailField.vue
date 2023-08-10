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
            lists: [{code: 0, name: '加载中...'}],
        }
    },

    methods: {
        loadLists() {
            if (this.field.value.length === 0) {
                this.lists = [];
                return;
            }

            Nova
                .request()
                .get(this.field.options)
                .then(response => {
                    this.handleLists(response.data.resources);
                });
        },

        // 筛选有效数据
        handleLists(lists) {
            let list = [];
            let pick = this.field.value;
            lists.map(item => {
                let code = item.value || item.id;
                if (pick.includes(code)) list.push(item);
            });
            this.formatLists(list);
        },

        // 格式化数据
        formatLists(lists) {
            if (lists.length === 0) {
                this.lists = [];
                return;
            }

            const _mix = this.field.nameWithCode || false;
            let list = [];
            lists.map(item => {
                let code = item.value || item.id
                let name = item.display || item.name;
                list.push({
                    code: code,
                    name: (_mix ? (code + ' - ') : '') + name
                });
            });
            this.lists = list;
        }
    },

    mounted() {
        if (typeof this.field.options !== 'string') {
            this.handleLists(this.field.options);
        } else if (this.field.belongsToMany) {
            this.formatLists(this.field.value);
        } else {
            this.loadLists()
        }
    },
}
</script>
