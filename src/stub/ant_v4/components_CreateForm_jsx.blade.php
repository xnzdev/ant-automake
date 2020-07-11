import { Form, Input, Modal } from 'antd';
import React from 'react';

const FormItem = Form.Item;
const formLayout = {
  labelCol: { span: 7 },
  wrapperCol: { span: 13 },
};

const CreateForm = props => {
  const { modalVisible, onSubmit: handleAdd, onCancel } = props;

  const [form] = Form.useForm();

  const okHandle = async () => {
    const fieldsValue = await form.validateFields();
    handleAdd(fieldsValue);
  };

  return (
    <Modal
      className="page-modal"
      destroyOnClose
      title="新建窗口"
      visible={modalVisible}
      onOk={okHandle}
      onCancel={() => onCancel()}
    >
      <Form {...formLayout} form={form}>
        @foreach($vars['fields_arr'] as $item)
        <?php if($item['Key'] == "PRI") continue; ?>
        <FormItem
          label="{{fieldsFormLabel($item['Comment'])}}"
          name="{{$item['Field']}}"
        >
          <Input />
        </FormItem>
        @endforeach
      </Form>
    </Modal>
  );
};

export default CreateForm;


