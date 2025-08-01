import React from 'react';
import { Card, CardContent, CardActions, Typography, Button, Box, Divider, LinearProgress } from '@mui/material';
import PropTypes from 'prop-types';

const CampaignCard = ({ campaign }) => {
  // Calculate budget utilization percentage
  const calculateSpentPercentage = (spent, budget) => {
    return Math.min(Math.round((spent / budget) * 100), 100);
  };

  return (
    <Card elevation={3} sx={{ 
      borderRadius: 2, 
      height: '100%', 
      display: 'flex', 
      flexDirection: 'column',
      backgroundColor: '#d1bca0ff',
      border: '1px solid #e0e0e0',
      boxShadow: '0px 4px 12px rgba(0, 0, 0, 0.1)'
    }}>
      <CardContent sx={{ flexGrow: 1 }}>
        {/* Campaign Title */}
        <Typography variant="h6" sx={{ 
          fontWeight: '700',
          mb: 1,
          color: '#155e5d',
          fontSize: '1.1rem',
          fontFamily: '"Roboto", "Helvetica", "Arial", sans-serif'
        }}>
          {campaign.title}
        </Typography>
        
        {/* Status and Start Date */}
        <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 1 }}>
          <Typography variant="body2" sx={{ color: '#555' }}>
            Status: <span style={{ fontWeight: '600', color: '#155e5d' }}>{campaign.status}</span>
          </Typography>
          <Typography variant="body2" sx={{ color: '#555' }}>
            Start Date: <span style={{ fontWeight: '600', color: '#155e5d' }}>{campaign.startDate}</span>
          </Typography>
        </Box>
        
        <Divider sx={{ my: 1, borderColor: '#e0e0e0' }} />
        
        {/* Financial Summary */}
        <Box sx={{ mt: 2 }}>
          <Typography variant="subtitle2" sx={{ 
            fontWeight: '600',
            color: '#155e5d',
            mb: 1,
            fontSize: '0.9rem'
          }}>
            Financial Summary:
          </Typography>
          
          <Box sx={{ display: 'flex', justifyContent: 'space-between', mt: 1 }}>
            <Typography variant="body2" sx={{ color: '#555' }}>
              Income: <span style={{ fontWeight: '600', color: '#155e5d' }}>{campaign.income.toLocaleString()} SP</span>
            </Typography>
            <Typography variant="body2" sx={{ color: '#555' }}>
              Expenses: <span style={{ fontWeight: '600', color: '#155e5d' }}>{campaign.expenses.toLocaleString()} SP</span>
            </Typography>
          </Box>
          
          <Box sx={{ display: 'flex', justifyContent: 'space-between', mt: 1 }}>
            <Typography variant="body2" sx={{ color: '#555' }}>
              Available Balance: <span style={{ fontWeight: '600', color: '#155e5d' }}>{campaign.balance.toLocaleString()} SAR</span>
            </Typography>
            <Typography variant="body2" sx={{ color: '#555' }}>
              Allocated Budget: <span style={{ fontWeight: '600', color: '#155e5d' }}>{campaign.budget.toLocaleString()} SAR</span>
            </Typography>
          </Box>
          
          {/* Progress Bar */}
          <LinearProgress 
            variant="determinate" 
            value={calculateSpentPercentage(campaign.expenses, campaign.budget)} 
            sx={{ 
              height: 10, 
              borderRadius: 5, 
              mt: 2,
              backgroundColor: '#f0f0f0',
              '& .MuiLinearProgress-bar': {
                backgroundColor: campaign.balance > 0 ? '#155e5d' : '#d2b48c'
              }
            }} 
          />
          
          <Typography variant="body2" sx={{ 
            textAlign: 'center', 
            mt: 1, 
            color: '#1a746e',
            fontWeight: '500'
          }}>
            Utilization: {calculateSpentPercentage(campaign.expenses, campaign.budget)}%
          </Typography>
        </Box>
        
        <Divider sx={{ my: 2, borderColor: '#e0e0e0' }} />
        
        {/* Last Updated */}
        <Typography variant="body2" sx={{ 
          textAlign: 'center', 
          fontStyle: 'italic', 
          color: '#666',
          fontSize: '0.8rem'
        }}>
          Last Updated: {campaign.lastUpdate}
        </Typography>
      </CardContent>
      
      {/* Card Buttons */}
      <CardActions sx={{ 
        justifyContent: 'center', 
        p: 2,
        pt: 0
      }}>
        <Button 
          size="medium"
          variant="contained" 
          sx={{ 
            backgroundColor: '#155e5d',
            '&:hover': {
              backgroundColor: '#155e5d',
              transform: 'translateY(-1px)'
            },
            fontWeight: '600',
            fontSize: '0.85rem',
            px: 2,
            py: 1,
            boxShadow: 'none',
            transition: 'all 0.2s ease'
          }}
        >
          View Statement
        </Button>
        
        {/* <Button 
          size="medium"
          variant="outlined" 
          sx={{ 
            ml: 2,
            color: '#155e5d',
            borderColor: '#155e5d',
            '&:hover': {
              backgroundColor: 'rgba(26, 116, 110, 0.05)',
              borderColor: '#155e5d',
              transform: 'translateY(-1px)'
            },
            fontWeight: '600',
            fontSize: '0.85rem',
            px: 2,
            py: 1,
            transition: 'all 0.2s ease'
          }}
        >
          Details
        </Button> */}
      </CardActions>
    </Card>
  );
};

CampaignCard.propTypes = {
  campaign: PropTypes.shape({
    id: PropTypes.number.isRequired,
    title: PropTypes.string.isRequired,
    status: PropTypes.string.isRequired,
    startDate: PropTypes.string.isRequired,
    income: PropTypes.number.isRequired,
    expenses: PropTypes.number.isRequired,
    budget: PropTypes.number.isRequired,
    balance: PropTypes.number.isRequired,
    lastUpdate: PropTypes.string.isRequired
  }).isRequired
};

export default CampaignCard;