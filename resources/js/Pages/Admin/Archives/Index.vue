<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import { ref, h } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import {
    RedoOutlined,
    EditFilled,
    ExclamationCircleFilled,
} from "@ant-design/icons-vue";
import dayjs from "dayjs";
import { composables } from "@/Composables/index.js";
import { Modal, message, notification } from "ant-design-vue";
import TableComponent from "@/Components/Table.vue";
const [modal] = Modal.useModal();

const props = defineProps({
    archives: Object,
});

const { sections, grades, strands } = composables();
const form = useForm({
    last_name: null,
    name: null,
    middle_name: null,
    suffix_name: null,
    lrn: null,
    birthday: null,
    age: null,
    contact_number: null,
    gender: null,
    grade: null,
    section: null,
    province: null,
    municipality: null,
    barangay: null,
    id_number: null,
    password: null,
    confirmation: null,
});

const columns = ref([
    {
        title: "Last Name",
        dataIndex: "last_name",
        key: "last_name",
    },
    {
        title: "First Name",
        dataIndex: "name",
        key: "name",
    },
    {
        title: "Middle Name",
        dataIndex: "middle_name",
        key: "middle_name",
    },

    {
        title: "Suffix Name",
        dataIndex: "suffix_name",
        key: "suffix_name",
    },
    {
        title: "Grade",
        dataIndex: "grade",
        key: "grade",
    },
    {
        title: "Section",
        dataIndex: "section",
        key: "section",
    },
    {
        title: "Actions",
        dataIndex: "actions",
        key: "actions",
        width: 8,
    },
]);

const showModal = ref(false);
const isEditing = ref(false);
const loading = ref(false);

const handleAdd = () => {
    showModal.value = true;
    isEditing.value = false;
};

const handleCancel = () => {
    form.reset();
    form.errors = {};
    showModal.value = false;
};

const handleEdit = (val) => {
    const data = dayjs(val.birthday, "YYYY/MM/DD");
    Object.entries(val).forEach(([key, value]) => {
        form[key] = value;
    });
    form.password = null;
    form.birthday = data;

    showModal.value = true;
    isEditing.value = true;
};

const submit = () => {
    form.post(route("students.store"), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            showModal.value = false;
        },
    });
};

const update = () => {
    form.put(route("students.update", form.id), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            showModal.value = false;
        },
    });
};

const handleDelete = (val) => {
    Modal.confirm({
        title: "Are you sure to restore this to student?",
        icon: h(ExclamationCircleFilled),
        content: "restored students can be accessed in Students page.",
        okText: "OK",
        onOk() {
            router.put(route("student.archive-restore", val.id));
            notification.success({
                placement: "topRight",
                duration: 3,
                rtl: true,
                message: "Successfully Restored",
                description: "Restored Student can be access in Students Page.",
            });
        },
        cancelText: "Cancel",
    });
};

const refresh = () => {
    router.reload({
        onStart: () => {
            loading.value = true;
        },
        onFinish: () => {
            loading.value = false;
        },
    });
};
</script>
<template>
    <AuthenticatedLayout>
        <div>
            <div class="page-title height-md:mb-30">Archives</div>

            <div>
                <TableComponent
                    :dataSource="props.archives.data"
                    :columns="columns"
                    :isLoading="loading"
                    :paginationData="props.archives.meta"
                >
                    <template #actionButtons>
                        <div class="flex justify-between">
                            <div>
                                <a-button @click="refresh()">Refresh</a-button>
                            </div>
                        </div>
                    </template>
                    <template #customColumn="slotProps">
                        <template
                            v-if="slotProps.column.dataIndex === 'actions'"
                        >
                            <div class="flex space-x-4">
                                <div @click="handleEdit(slotProps.record)">
                                    <a-tooltip placement="topLeft">
                                        <template #title>
                                            <span>Edit</span>
                                        </template>
                                        <a-button><EditFilled /></a-button>
                                    </a-tooltip>
                                </div>
                                <div @click="handleDelete(slotProps.record)">
                                    <a-tooltip placement="topLeft">
                                        <template #title>
                                            <span>Restore</span>
                                        </template>
                                        <a-button><RedoOutlined /></a-button>
                                    </a-tooltip>
                                </div>
                            </div>
                        </template>
                    </template>
                </TableComponent>
            </div>
        </div>
        <a-modal
            v-model:open="showModal"
            :title="isEditing ? 'Edit Student' : 'Add Student'"
            width="1000px"
            :footer="null"
            :afterClose="handleCancel"
        >
            <a-form :model="form" name="basic" layout="vertical">
                <a-card title="Personal Details" class="bg-gray-200">
                    <div class="flex justify-between mx-auto space-x-4">
                        <a-form-item required label="First Name" name="name">
                            <a-input v-model:value="form.name" />
                            <InputError
                                class="mt-2"
                                :message="form.errors.name"
                            />
                        </a-form-item>
                        <a-form-item
                            required
                            label="Middle Name"
                            name="middle_name"
                        >
                            <a-input v-model:value="form.middle_name" />
                            <InputError
                                class="mt-2"
                                :message="form.errors.middle_name"
                            />
                        </a-form-item>
                        <a-form-item
                            required
                            label="Last Name"
                            name="last_name"
                        >
                            <a-input v-model:value="form.last_name" />
                            <InputError
                                class="mt-2"
                                :message="form.errors.last_name"
                            />
                        </a-form-item>
                        <a-form-item label="Suffix Name" name="suffix_name">
                            <a-input v-model:value="form.suffix_name" />
                            <InputError
                                class="mt-2"
                                :message="form.errors.suffix_name"
                            />
                        </a-form-item>
                    </div>
                    <div class="flex justify-between mx-auto space-x-4">
                        <a-form-item required label="LRN" name="lrn">
                            <a-input v-model:value="form.lrn" />
                            <InputError
                                class="mt-2"
                                :message="form.errors.lrn"
                            />
                        </a-form-item>
                        <a-form-item
                            required
                            label="Date of Birth"
                            name="birthday"
                        >
                            <a-date-picker
                                v-model:value="form.birthday"
                                format="YYYY/MM/DD"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.birthday"
                            />
                        </a-form-item>
                        <a-form-item required label="Age" name="age">
                            <a-input v-model:value="form.age" />
                            <InputError
                                class="mt-2"
                                :message="form.errors.age"
                            />
                        </a-form-item>
                        <a-form-item
                            required
                            label="Contact Number"
                            name="contact_number"
                        >
                            <a-input v-model:value="form.contact_number" />
                            <InputError
                                class="mt-2"
                                :message="form.errors.contact_number"
                            />
                        </a-form-item>
                        <a-form-item required label="Gender" name="gender">
                            <a-select ref="select" v-model:value="form.gender">
                                <a-select-option value="Male"
                                    >Male</a-select-option
                                >
                                <a-select-option value="Female"
                                    >Female</a-select-option
                                >
                            </a-select>
                            <InputError
                                class="mt-2"
                                :message="form.errors.gender"
                            />
                        </a-form-item>
                    </div>
                    <div class="flex mx-auto space-x-4">
                        <a-form-item
                            required
                            label="Grade"
                            name="grade"
                            class="w-full"
                        >
                            <a-select
                                v-model:value="form.grade"
                                :options="grades()"
                                :filter-option="
                                    (input, option) =>
                                        option.label
                                            .toLowerCase()
                                            .indexOf(input.toLowerCase()) >= 0
                                "
                            >
                            </a-select>
                            <InputError
                                class="mt-2"
                                :message="form.errors.grade"
                            />
                        </a-form-item>
                        <a-form-item
                            required
                            :label="form.grade > 10 ? 'Strand' : 'Section'"
                            class="w-full"
                        >
                            <a-select
                                v-model:value="form.section"
                                :options="
                                    form.grade > 10 ? strands() : sections()
                                "
                                :filter-option="
                                    (input, option) =>
                                        option.label
                                            .toLowerCase()
                                            .indexOf(input.toLowerCase()) >= 0
                                "
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.section"
                            />
                        </a-form-item>
                    </div>
                </a-card>

                <a-card title="Address" class="mt-4 bg-gray-200">
                    <div class="flex justify-between mx-auto space-x-4">
                        <a-form-item required label="Province">
                            <a-input v-model:value="form.province" />
                            <InputError
                                class="mt-2"
                                :message="form.errors.province"
                            />
                        </a-form-item>
                        <a-form-item required label="Municipality">
                            <a-input v-model:value="form.municipality" />
                            <InputError
                                class="mt-2"
                                :message="form.errors.municipality"
                            />
                        </a-form-item>
                        <a-form-item required label="Barangay">
                            <a-input v-model:value="form.barangay" />
                            <InputError
                                class="mt-2"
                                :message="form.errors.barangay"
                            />
                        </a-form-item>
                    </div>
                    <a-form-item required label="ID">
                        <a-input v-model:value="form.id_number" />
                        <InputError
                            class="mt-2"
                            :message="form.errors.id_number"
                        />
                    </a-form-item>
                    <a-form-item v-if="isEditing" required label="Password">
                        <a-input
                            type="password"
                            v-model:value="form.password"
                            placeholder="********"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.password"
                        />
                    </a-form-item>
                    <a-form-item
                        v-if="isEditing"
                        required
                        label="Confirm Password"
                    >
                        <a-input
                            type="password"
                            v-model:value="form.confirmation"
                            placeholder="********"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.confirmation"
                        />
                    </a-form-item>
                </a-card>
                <div class="flex justify-end mt-5">
                    <a-button class="mr-2" @click.prevent="handleCancel"
                        >Cancel</a-button
                    >
                    <a-button
                        type="primary"
                        :loading="form.processing"
                        @click.prevent="isEditing ? update() : submit()"
                        >{{ isEditing ? "Update" : "Submit" }}</a-button
                    >
                </div>
            </a-form>
        </a-modal>
    </AuthenticatedLayout>
</template>
