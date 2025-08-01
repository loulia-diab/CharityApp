import React from 'react';
import {
  Card,
  CardContent,
  Typography,
  Divider,
  Box,
  Avatar,
  Chip,
  Stack,
  List,
  ListItem,
  ListItemText
} from '@mui/material';

// بيانات المتطوع من ملف JSON
const volunteerData = {
  "data": {
    "id": 1,
    "user_id": null,
    "admin_id": 1,
    "full_name": "سهيلة محمد",
    "gender": "أنثى",
    "birth_date": "1995-06-15",
    "address": "الرياض، السعودية",
    "study_qualification": "بكالوريوس هندسة",
    "job": "مهندس برمجيات",
    "preferred_times": "مساءً",
    "has_previous_volunteer": 1,
    "previous_volunteer": "شاركت في حملات بيئية",
    "phone": "0551234567",
    "notes": "أرغب بالمساعدة في الأنشطة التقنية",
    "status": "قيد الانتظار",
    "reason_of_rejection": null,
    "days": [
      {"id": 3, "name": "الثلاثاء"},
      {"id": 5, "name": "الخميس"}
    ],
    "types": [
      {"id": 3, "name": "توعوي"}
    ],
    "created_at": "2025-07-19 07:38:23"
  }
};

const VolunteerInfoCard = () => {
  // دالة لتنسيق التاريخ
  const formatDate = (dateString) => {
    if (!dateString) return 'غير محدد';
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('ar-SA', options);
  };

  // دالة لتنسيق التاريخ والوقت
  const formatDateTime = (dateTimeString) => {
    if (!dateTimeString) return 'غير محدد';
    const options = { 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    };
    return new Date(dateTimeString).toLocaleDateString('ar-SA', options);
  };

  return (
    <Box sx={{ display: 'flex', justifyContent: 'center', p: 3 }}>
      <Card sx={{ 
        width: 800,
        borderRadius: 3,
        boxShadow: 4,
        bgcolor: '#ffffff'
      }}>
        {/* رأس البطاقة */}
        <Box sx={{
          bgcolor: '#155e5d',
          color: 'white',
          p: 3,
          textAlign: 'center',
          borderTopLeftRadius: 8,
          borderTopRightRadius: 8
        }}>
          <Avatar sx={{
            width: 100,
            height: 100,
            mb: 2,
            bgcolor: '#d2b48c',
            fontSize: '2.5rem',
            margin: '0 auto'
          }}>
            {volunteerData.data.full_name.split(' ').map(n => n[0]).join('')}
          </Avatar>
          <Typography variant="h4" gutterBottom>
            {volunteerData.data.full_name}
          </Typography>
          <Typography variant="h6">
            رقم المتطوع: {volunteerData.data.id}
          </Typography>
        </Box>

        {/* محتوى البطاقة */}
        <CardContent>
          <Stack spacing={2}>

            {/* المعلومات الشخصية */}
            <Typography variant="h6" color="#155e5d" sx={{ mt: 2 }}>
              المعلومات الشخصية
            </Typography>
            <Divider sx={{ bgcolor: '#e0e0e0', mb: 2 }} />
            
            <Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 3 }}>
              <Box sx={{ flex: 1, minWidth: 200 }}>
                <Typography><strong>الجنس:</strong> {volunteerData.data.gender}</Typography>
                <Typography><strong>تاريخ الميلاد:</strong> {formatDate(volunteerData.data.birth_date)}</Typography>
              </Box>
              <Box sx={{ flex: 1, minWidth: 200 }}>
                <Typography><strong>العنوان:</strong> {volunteerData.data.address}</Typography>
                <Typography><strong>الهاتف:</strong> {volunteerData.data.phone}</Typography>
              </Box>
            </Box>

            {/* المعلومات المهنية */}
            <Typography variant="h6" color="#155e5d" sx={{ mt: 3 }}>
              المعلومات المهنية
            </Typography>
            <Divider sx={{ bgcolor: '#e0e0e0', mb: 2 }} />
            
            <Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 3 }}>
              <Box sx={{ flex: 1, minWidth: 200 }}>
                <Typography><strong>المؤهل العلمي:</strong> {volunteerData.data.study_qualification}</Typography>
              </Box>
              <Box sx={{ flex: 1, minWidth: 200 }}>
                <Typography><strong>المهنة:</strong> {volunteerData.data.job}</Typography>
              </Box>
            </Box>

            {/* معلومات التطوع */}
            <Typography variant="h6" color="#155e5d" sx={{ mt: 3 }}>
              معلومات التطوع
            </Typography>
            <Divider sx={{ bgcolor: '#e0e0e0', mb: 2 }} />
            
            <Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 3 }}>
              <Box sx={{ flex: 1, minWidth: 200 }}>
                <Typography>
                  <strong>الحالة:</strong> 
                  <Chip 
                    label={volunteerData.data.status} 
                    sx={{ 
                      ml: 1, 
                      bgcolor: volunteerData.data.status === 'مقبول' ? '#4caf50' : 
                              volunteerData.data.status === 'مرفوض' ? '#f44336' : '#ff9800',
                      color: 'white'
                    }} 
                  />
                </Typography>
                <Typography>
                  <strong>خبرة تطوعية سابقة:</strong> 
                  {volunteerData.data.has_previous_volunteer ? ' نعم' : ' لا'}
                </Typography>
                {volunteerData.data.has_previous_volunteer && (
                  <Typography><strong>التجارب السابقة:</strong> {volunteerData.data.previous_volunteer}</Typography>
                )}
              </Box>
              <Box sx={{ flex: 1, minWidth: 200 }}>
                <Typography><strong>الأوقات المفضلة:</strong> {volunteerData.data.preferred_times}</Typography>
              </Box>
            </Box>

            {/* الأيام والأنواع */}
            <Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 3, mt: 2 }}>
              <Box sx={{ flex: 1, minWidth: 200 }}>
                <Typography><strong>الأيام المتاحة:</strong></Typography>
                <List dense>
                  {volunteerData.data.days.map(day => (
                    <ListItem key={day.id} sx={{ py: 0 }}>
                      <ListItemText primary={`- ${day.name}`} />
                    </ListItem>
                  ))}
                </List>
              </Box>
              <Box sx={{ flex: 1, minWidth: 200 }}>
                <Typography><strong>مجالات التطوع:</strong></Typography>
                <List dense>
                  {volunteerData.data.types.map(type => (
                    <ListItem key={type.id} sx={{ py: 0 }}>
                      <ListItemText primary={`- ${type.name}`} />
                    </ListItem>
                  ))}
                </List>
              </Box>
            </Box>

            {/* ملاحظات إضافية */}
            {volunteerData.data.notes && (
              <>
                <Typography variant="h6" color="#155e5d" sx={{ mt: 3 }}>
                  ملاحظات إضافية
                </Typography>
                <Divider sx={{ bgcolor: '#e0e0e0', mb: 2 }} />
                <Typography>{volunteerData.data.notes}</Typography>
              </>
            )}

            {/* معلومات النظام */}
            <Typography variant="h6" color="#155e5d" sx={{ mt: 3 }}>
              معلومات النظام
            </Typography>
            <Divider sx={{ bgcolor: '#e0e0e0', mb: 2 }} />
            <Typography><strong>تاريخ التسجيل:</strong> {formatDateTime(volunteerData.data.created_at)}</Typography>
          </Stack>
        </CardContent>
      </Card>
    </Box>
  );
};

export default VolunteerInfoCard;