import { Form, Input, Modal } from 'antd';
import React from 'react';

const FormItem = Form.Item;
const formLayout = {
  labelCol: { span: 7 },
  wrapperCol: { span: 13 },
};

const UpdateForm = props => {
  const { updateModalVisible, onSubmit: handleUpdate, onCancel, values } = props;
  const [form] = Form.useForm();

  const okHandle = async () => {
    const fieldsValue = await form.validateFields();
    handleUpdate(fieldsValue);
  };

  return (
    <Modal
      className="page-modal"
      destroyOnClose
      title="编辑窗口"
      visible={updateModalVisible}
      onOk={okHandle}
      onCancel={() => onCancel()}
    >
      <Form
        {...formLayout}
        form={form}
        initialValues=<?='{{'?>

          @foreach($vars['fields_arr'] as $item)
            {{$item['Field']}}: values.{{$item['Field']}},
          @endforeach
        <?='}}'?>
      >
        @foreach($vars['fields_arr'] as $item)
        @if($item['Key'] == "PRI")
        <FormItem
          style=@{{ display:"none" }}
          name="{{$item['Field']}}"
        >
          <Input type="hidden" />
        </FormItem>
        @else
        <FormItem
          label="{{fieldsFormLabel($item['Comment'])}}"
          name="{{$item['Field']}}"
        >
          <Input />
        </FormItem>
        @endif
        @endforeach
      </Form>
    </Modal>
  );
};


export default UpdateForm;
