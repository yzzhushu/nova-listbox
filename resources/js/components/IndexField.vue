<template>
    <div :class="`text-${field.textAlign}`">
        <div v-if="field.value.length > 0">
            <Tooltip
                :triggers="['hover']"
                :popperTriggers="['hover']"
                placement="top"
                theme="plain"
                @show="loadLists"
                :auto-hide="true"
                distance="12"
            >
                <template #default>
                    <span class="link-default">{{ field.displayedAs || field.value.length }}</span>
                </template>
                <template #content>
                    <ul
                        style="max-height: 232px;overflow: scroll;"
                        class="py-3 min-w-[20rem] max-w-2xl"
                    >
                        <li v-for="item in lists"
                            style="height: 40px;line-height: 40px;"
                            class="cursor-pointer list-box-item text-center px-6">
                            {{ item.name }}
                        </li>
                    </ul>
                </template>
            </Tooltip>
        </div>
        <p v-else>&mdash;</p>
    </div>
</template>

<script>
import request from "../request";
export default {
    mixins: [request],

    methods: {
        loadLists() {
            if (this.loading) return;
            this.loading = true;

            Nova
                .request()
                .get(this.field.options)
                .then(response => {
                    this.handleLists(response.data.resources);
                });
        },
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
