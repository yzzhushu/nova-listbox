<template>
    <div :class="`text-${field.textAlign}`">
        <Dropdown v-if="field.value.length > 0">
            <DropdownTrigger
                class="text-gray-500 inline-flex items-center cursor-pointer"
                :showArrow="false"
                v-on:hover="loadLists"
            >
                <span class="link-default font-bold">{{display}}</span>
            </DropdownTrigger>

            <template #menu>
                <DropdownMenu width="auto">
                    <ul
                        style="width: auto;max-width: 320px;max-height: 232px;overflow: scroll;"
                        class="py-3"
                    >
                        <li v-for="item in lists"
                            style="height: 40px;line-height: 40px;"
                            class="cursor-pointer list-box-item text-center px-6">
                            {{ item.name }}
                        </li>
                    </ul>
                </DropdownMenu>
            </template>
        </Dropdown>
        <p v-else>&mdash;</p>
    </div>
</template>

<script>
export default {
    props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

    data() {
        return {
            lists: [{code: 0, name: '加载中...'}],
            loading: false
        }
    },

    computed: {
        display() {
            return this.field.displayedAs || this.field.value.length;
        }
    },

    methods: {
        loadLists() {
            if (this.loading) return;
            if (this.field.value.length === 0) return;
            this.loading = true;

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
                this.lists = [{code: 0, name: '暂无数据'}];
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
            this.loading = true;
            this.handleLists(this.field.options);
        } else if (this.field.belongsToMany) {
            this.loading = true;
            this.formatLists(this.field.value);
        }
    }
}
</script>
