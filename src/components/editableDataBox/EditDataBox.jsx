import React, { useState } from 'react';
import { 
  Box, 
  Typography, 
  TextField, 
  Button, 
  Paper, 
  IconButton,
  CircularProgress,
  Avatar,
  Divider
} from '@mui/material';
import EditIcon from '@mui/icons-material/Edit';
import SaveIcon from '@mui/icons-material/Save';
import CancelIcon from '@mui/icons-material/Cancel';
import CloudUploadIcon from '@mui/icons-material/CloudUpload';
import { styled } from '@mui/material/styles';

const VisuallyHiddenInput = styled('input')({
  clip: 'rect(0 0 0 0)',
  clipPath: 'inset(50%)',
  height: 1,
  overflow: 'hidden',
  position: 'absolute',
  bottom: 0,
  left: 0,
  whiteSpace: 'nowrap',
  width: 1,
});

// تنسيق مخصص لحقول النص
const StyledTextField = styled(TextField)(({ theme }) => ({
  '& .MuiOutlinedInput-root': {
    backgroundColor: '#d1bca0ff', // خلفية بيضاء
    borderRadius: '8px',
    '& fieldset': {
      borderColor: '#218483ff', // لون الحدود
    },
    '&:hover fieldset': {
      borderColor: '#218483ff', // لون الحدود عند hover
    },
    '&.Mui-focused fieldset': {
      borderColor: '#218483ff', // لون الحدود عند التركيز
    },
  },
}));

const EditDataBox = () => {
  // بيانات وهمية للعرض
  const mockData = {
    name: 'حملة التبرع للمدرسة',
    description: 'هذه الحملة تهدف إلى تجهيز الفصول الدراسية بالأثاث والأدوات التعليمية اللازمة',
    amount: 5000,
    collectedAmount: 4000,
    remainingAmount: 1000,
    image: 'https://via.placeholder.com/300x200?text=صورة+الحملة'
  };

  const BOX_COLOR = '#d1bca0ff'; 

  const [editing, setEditing] = useState(false);
  const [editedData, setEditedData] = useState(mockData);
  const [loading, setLoading] = useState(false);

  const handleChange = (field) => (e) => {
    setEditedData({ ...editedData, [field]: e.target.value });
  };

  const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onloadend = () => {
        setEditedData({ ...editedData, image: reader.result });
      };
      reader.readAsDataURL(file);
    }
  };

  const handleSave = () => {
    setLoading(true);
    setTimeout(() => {
      console.log('بيانات تم حفظها (وهمي):', editedData);
      setLoading(false);
      setEditing(false);
      alert('تم الحفظ بنجاح (وهمي)');
    }, 1000);
  };

  const handleCancel = () => {
    setEditedData(mockData);
    setEditing(false);
  };

  return (
    <Box sx={{ p: 4, maxWidth: 1000, mx: 'auto' }}>
      <Paper elevation={3} sx={{ 
        p: 3, 
        mb: 3,
        backgroundColor: BOX_COLOR
      }}>
        <Box display="flex" justifyContent="space-between" alignItems="center" mb={2}>
          <Typography variant="h6">معلومات الحملة </Typography>
          {!editing ? (
            <IconButton onClick={() => setEditing(true)} color="primary">
              <EditIcon />
            </IconButton>
          ) : (
            <Box>
              <IconButton onClick={handleSave} disabled={loading}>
                {loading ? <CircularProgress size={24} /> : <SaveIcon color="primary" />}
              </IconButton>
              <IconButton onClick={handleCancel} disabled={loading}>
                <CancelIcon color="error" />
              </IconButton>
            </Box>
          )}
        </Box>

        {editing ? (
          <Box component="form" sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
            <Box sx={{ display: 'flex', gap: 2 }}>
              <Avatar
                src={editedData.image}
                variant="rounded"
                sx={{ width: 150, height: 100 }}
              />
              <Button
                component="label"
                variant="contained"
                startIcon={<CloudUploadIcon />}
                sx={{ alignSelf: 'center' }}
              >
                تغيير الصورة
                <VisuallyHiddenInput 
                  type="file" 
                  accept="image/*"
                  onChange={handleImageChange}
                />
              </Button>
            </Box>
            
            {/* استخدام الحقول المخصصة */}
            <StyledTextField
              label="اسم الحملة"
              value={editedData.name}
              onChange={handleChange('name')}
              fullWidth
            />
            <StyledTextField
              label="الوصف"
              value={editedData.description}
              onChange={handleChange('description')}
              multiline
              rows={4}
              fullWidth
            />
            <Box sx={{ display: 'flex', gap: 2 }}>
              <StyledTextField
                label="المبلغ المطلوب ($)"
                value={editedData.amount}
                onChange={handleChange('amount')}
                type="number"
                fullWidth
              />
              <StyledTextField
                label="المبلغ المجموع ($)"
                value={editedData.collectedAmount}
                onChange={handleChange('collectedAmount')}
                type="number"
                fullWidth
              />
            </Box>
          </Box>
        ) : (
          <Box>
            <Box sx={{ display: 'flex', gap: 3, mb: 2 }}>
              <Avatar
                src={mockData.image}
                variant="rounded"
                sx={{ width: 150, height: 100 }}
              />
              <Box>
                <Typography><strong>الاسم:</strong> {mockData.name}</Typography>
                <Typography sx={{ mt: 1 }}><strong>الوصف:</strong> {mockData.description}</Typography>
              </Box>
            </Box>
            
            <Divider sx={{ my: 2 }} />
            
            <Box sx={{ display: 'flex', justifyContent: 'space-between' }}>
              <Box>
                <Typography><strong>المبلغ المطلوب:</strong> {mockData.amount} $</Typography>
                <Typography sx={{ mt: 1 }}><strong>المبلغ المجموع:</strong> {mockData.collectedAmount} $</Typography>
                <Typography sx={{ mt: 1 }}><strong>المبلغ المتبقي:</strong> {mockData.remainingAmount} $</Typography>
              </Box>
              
              <Box sx={{ width: '60%' }}>
                <Box sx={{ 
                  height: 20, 
                  backgroundColor: '#e0e0e0', 
                  borderRadius: 10,
                  overflow: 'hidden'
                }}>
                  <Box sx={{ 
                    width: `${(mockData.collectedAmount / mockData.amount) * 100}%`, 
                    height: '100%', 
                    backgroundColor: '#4caf50' 
                  }} />
                </Box>
                <Typography variant="caption" sx={{ mt: 1, display: 'block' }}>
                  تم جمع {Math.round((mockData.collectedAmount / mockData.amount) * 100)}% من الهدف
                </Typography>
              </Box>
            </Box>
          </Box>
        )}
      </Paper>
    </Box>
  );
};

export default EditDataBox;