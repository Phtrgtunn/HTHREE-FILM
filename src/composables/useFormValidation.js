import { reactive, computed } from 'vue';

export function useFormValidation() {
  const errors = reactive({});
  const touched = reactive({});

  // Validation rules
  const rules = {
    required: (value, fieldName = 'Trường này') => {
      if (!value || (typeof value === 'string' && !value.trim())) {
        return `${fieldName} không được để trống`;
      }
      return null;
    },

    email: (value) => {
      if (!value) return null;
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(value)) {
        return 'Email không hợp lệ';
      }
      return null;
    },

    phone: (value) => {
      if (!value) return null;
      // Vietnamese phone number: 0xxxxxxxxx or +84xxxxxxxxx
      const phoneRegex = /^(0|\+84)[0-9]{9,10}$/;
      if (!phoneRegex.test(value.replace(/\s/g, ''))) {
        return 'Số điện thoại không hợp lệ (VD: 0901234567)';
      }
      return null;
    },

    minLength: (min) => (value, fieldName = 'Trường này') => {
      if (!value) return null;
      if (value.length < min) {
        return `${fieldName} phải có ít nhất ${min} ký tự`;
      }
      return null;
    },

    maxLength: (max) => (value, fieldName = 'Trường này') => {
      if (!value) return null;
      if (value.length > max) {
        return `${fieldName} không được vượt quá ${max} ký tự`;
      }
      return null;
    },

    pattern: (regex, message) => (value) => {
      if (!value) return null;
      if (!regex.test(value)) {
        return message || 'Định dạng không hợp lệ';
      }
      return null;
    },

    match: (otherValue, fieldName) => (value) => {
      if (value !== otherValue) {
        return `Không khớp với ${fieldName}`;
      }
      return null;
    },

    custom: (validatorFn) => (value) => {
      return validatorFn(value);
    },

    password: (value) => {
      if (!value) return null;
      
      const errors = [];
      
      if (value.length < 8) {
        errors.push('ít nhất 8 ký tự');
      }
      
      if (!/[a-zA-Z]/.test(value)) {
        errors.push('có chữ cái');
      }
      
      if (!/[0-9]/.test(value)) {
        errors.push('có số');
      }
      
      if (errors.length > 0) {
        return `Mật khẩu phải ${errors.join(', ')}`;
      }
      
      return null;
    },

    passwordStrength: (value) => {
      if (!value) return { strength: 0, label: '', color: '' };
      
      let strength = 0;
      
      // Length
      if (value.length >= 8) strength += 1;
      if (value.length >= 12) strength += 1;
      
      // Character types
      if (/[a-z]/.test(value)) strength += 1;
      if (/[A-Z]/.test(value)) strength += 1;
      if (/[0-9]/.test(value)) strength += 1;
      if (/[^a-zA-Z0-9]/.test(value)) strength += 1;
      
      const labels = {
        0: { label: 'Rất yếu', color: 'red' },
        1: { label: 'Rất yếu', color: 'red' },
        2: { label: 'Yếu', color: 'orange' },
        3: { label: 'Trung bình', color: 'yellow' },
        4: { label: 'Mạnh', color: 'lime' },
        5: { label: 'Rất mạnh', color: 'green' },
        6: { label: 'Xuất sắc', color: 'green' }
      };
      
      return { strength, ...labels[strength] };
    }
  };

  // Validate a single field
  const validateField = (fieldName, value, validationRules, fieldLabel) => {
    touched[fieldName] = true;
    
    if (!validationRules || validationRules.length === 0) {
      errors[fieldName] = null;
      return true;
    }

    for (const rule of validationRules) {
      const error = rule(value, fieldLabel);
      if (error) {
        errors[fieldName] = error;
        return false;
      }
    }

    errors[fieldName] = null;
    return true;
  };

  // Validate all fields
  const validateForm = (formData, validationSchema) => {
    let isValid = true;

    Object.keys(validationSchema).forEach((fieldName) => {
      const { value, rules: fieldRules, label } = validationSchema[fieldName];
      const fieldValid = validateField(fieldName, value, fieldRules, label);
      if (!fieldValid) {
        isValid = false;
      }
    });

    return isValid;
  };

  // Reset validation
  const resetValidation = () => {
    Object.keys(errors).forEach(key => {
      errors[key] = null;
    });
    Object.keys(touched).forEach(key => {
      touched[key] = false;
    });
  };

  // Check if form is valid
  const isFormValid = computed(() => {
    return Object.values(errors).every(error => !error);
  });

  // Check if field has error and is touched
  const hasError = (fieldName) => {
    return touched[fieldName] && errors[fieldName];
  };

  return {
    errors,
    touched,
    rules,
    validateField,
    validateForm,
    resetValidation,
    isFormValid,
    hasError
  };
}

// Example usage:
/*
import { useFormValidation } from '@/composables/useFormValidation';

const { errors, rules, validateField, validateForm, isFormValid } = useFormValidation();

const form = reactive({
  name: '',
  email: '',
  phone: ''
});

// Validate on blur
const onBlur = (fieldName) => {
  const validationSchema = {
    name: {
      value: form.name,
      rules: [rules.required, rules.minLength(2)],
      label: 'Họ tên'
    },
    email: {
      value: form.email,
      rules: [rules.required, rules.email],
      label: 'Email'
    },
    phone: {
      value: form.phone,
      rules: [rules.phone],
      label: 'Số điện thoại'
    }
  };
  
  validateField(fieldName, form[fieldName], validationSchema[fieldName].rules, validationSchema[fieldName].label);
};

// Validate on submit
const onSubmit = () => {
  const validationSchema = {
    name: {
      value: form.name,
      rules: [rules.required, rules.minLength(2)],
      label: 'Họ tên'
    },
    email: {
      value: form.email,
      rules: [rules.required, rules.email],
      label: 'Email'
    },
    phone: {
      value: form.phone,
      rules: [rules.phone],
      label: 'Số điện thoại'
    }
  };
  
  if (validateForm(form, validationSchema)) {
    // Submit form
  }
};
*/
