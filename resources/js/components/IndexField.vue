<template>
    <div :class="`text-${field.textAlign}`">
        <Dropdown v-if="field.value.length > 0">
            <DropdownTrigger
                class="text-gray-500 inline-flex items-center cursor-pointer"
                :showArrow="false"
                @click="loadLists"
            >
                <span class="link-default font-bold">{{ display }}</span>
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
            loading: false,
            display: ''
        }
    },

    methods: {
        loadLists() {
            if (this.loading) return;
            let lists = this.field.value;
            if (lists === null || lists.length === 0) return;
            this.loading = true;

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
            this.lists = list.length > 0 ? list : [{code: 0, name: '暂无数据'}];
        },
    },

    mounted() {
        this.display = this.field.displayedAs || this.field.value.length;

        if (this.field.belongsToMany) {         // BelongsToMany情况下，直接渲染列表
            this.loaded = true;
            this.formatLists(this.field.value);
        }
    },
}
</script>
