import React, { useState } from 'react';
import Box from '@mui/material/Box';
import Button from '@mui/material/Button';
import CloudUploadIcon from '@mui/icons-material/CloudUpload';
import InsertPhotoIcon from '@mui/icons-material/InsertPhoto';
import { styled } from '@mui/material/styles';
import Typography from '@mui/material/Typography';

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

const ImageUploadBox = ({ 
  width = '500px',
  height = '300px',
  label = 'اسحب وأسقط الصورة هنا',
  uploadLabel = 'اختر صورة',
  boxColor = '#f5f5f5',
  backgroundImage = null, // إمكانية إضافة صورة خلفية
  accept = 'image/*',
  onImageSelected, // دالة تستدعى عند اختيار صورة
  disabled = false
}) => {
  const [preview, setPreview] = useState(backgroundImage);
  const [dragActive, setDragActive] = useState(false);

  const handleFileChange = (event) => {
    const file = event.target.files?.[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onloadend = () => {
      setPreview(reader.result);
      if (onImageSelected) {
        onImageSelected(reader.result); // إرجاع بيانات الصورة كـ base64
      }
    };
    reader.readAsDataURL(file);
  };

  const handleDrag = (e) => {
    e.preventDefault();
    e.stopPropagation();
    if (e.type === 'dragenter' || e.type === 'dragover') {
      setDragActive(true);
    } else if (e.type === 'dragleave') {
      setDragActive(false);
    }
  };

  const handleDrop = (e) => {
    e.preventDefault();
    e.stopPropagation();
    setDragActive(false);
    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
      const file = e.dataTransfer.files[0];
      const reader = new FileReader();
      reader.onloadend = () => {
        setPreview(reader.result);
        if (onImageSelected) {
          onImageSelected(reader.result);
        }
      };
      reader.readAsDataURL(file);
    }
  };

  return (
    <Box
      sx={{ 
        width: width,
        height: height,
        backgroundColor: boxColor,
        borderRadius: '8px',
        padding: '16px',
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        justifyContent: 'center',
        gap: '16px',
        border: dragActive ? '2px dashed #1976d2' : '2px dashed #ccc',
        transition: 'all 0.3s ease',
        position: 'relative',
        overflow: 'hidden',
        backgroundImage: preview ? `url(${preview})` : undefined,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        backgroundRepeat: 'no-repeat'
      }}
      onDragEnter={handleDrag}
      onDragOver={handleDrag}
      onDragLeave={handleDrag}
      onDrop={handleDrop}
    >
      {!preview ? (
        <>
          <InsertPhotoIcon sx={{ fontSize: 64, color: 'action.disabled' }} />
          <Typography variant="body1" color="textSecondary">
            {label}
          </Typography>
          <Button
            component="label"
            variant="contained"
            color="primary"
            startIcon={<CloudUploadIcon />}
            disabled={disabled}
            sx={{
              backgroundColor: '#1976d2',
              '&:hover': {
                backgroundColor: '#1565c0'
              }
            }}
          >
            {uploadLabel}
            <VisuallyHiddenInput 
              type="file" 
              accept={accept}
              onChange={handleFileChange}
            />
          </Button>
        </>
      ) : (
        <Box sx={{
          position: 'absolute',
          bottom: 16,
          left: 0,
          right: 0,
          display: 'flex',
          justifyContent: 'center',
          backgroundColor: 'rgba(0, 0, 0, 0.5)',
          padding: '8px'
        }}>
          <Button
            component="label"
            variant="contained"
            color="primary"
            startIcon={<CloudUploadIcon />}
            disabled={disabled}
            sx={{
              backgroundColor: '#1976d2',
              '&:hover': {
                backgroundColor: '#1565c0'
              }
            }}
          >
            تغيير الصورة
            <VisuallyHiddenInput 
              type="file" 
              accept={accept}
              onChange={handleFileChange}
            />
          </Button>
        </Box>
      )}
    </Box>
  );
};

export default ImageUploadBox;