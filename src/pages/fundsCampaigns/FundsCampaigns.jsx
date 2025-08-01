import React from 'react';
import { Box, Typography, Paper, Grid, Button } from '@mui/material';
import Sidebar from '../../components/sidebar/Sidebar';
import Navbar from '../../components/navbar/Navbar';
import CampaignCard from '../../components/campaignCard/CampaignCard';
import './fundsCampaigns.scss';

const FundsCampaigns = () => {
  // Campaign data with financial details
  const campaigns = [
    {
      id: 1,
      title: "Orphan Education Campaign",
      status: "Active",
      startDate: "2023-10-01",
      income: 150000,
      expenses: 87500,
      budget: 200000,
      balance: 62500,
      lastUpdate: "2023-11-15"
    },
    {
      id: 2,
      title: "Ramadan Iftar Campaign",
      status: "Completed",
      startDate: "2023-04-01",
      income: 80000,
      expenses: 78000,
      budget: 75000,
      balance: 2000,
      lastUpdate: "2023-05-30"
    },
    {
      id: 3,
      title: "Winter Clothing Drive",
      status: "In Progress",
      startDate: "2023-11-01",
      income: 45000,
      expenses: 32000,
      budget: 60000,
      balance: 13000,
      lastUpdate: "2023-11-20"
    }
  ];

  // Calculate totals
  const totalIncome = campaigns.reduce((sum, campaign) => sum + campaign.income, 0);
  const totalExpenses = campaigns.reduce((sum, campaign) => sum + campaign.expenses, 0);
  const totalBalance = campaigns.reduce((sum, campaign) => sum + campaign.balance, 0);

  return (
    <div className='fundsCampaigns'>
      <Sidebar />
      <div className="fundsCampaignsContainer">
        <Navbar />
        
        <Box component="main" sx={{ flexGrow: 1, p: 3 }}>
          <Typography variant="h4" sx={{ mb: 4, fontWeight: 'bold', color: '#2e7d32' }}>
            Campaign Funds Management
          </Typography>
          
          {/* Quick Stats */}
          <Grid container spacing={3} sx={{ mb: 4 }}>
            <Grid item xs={12} md={4}>
              <Paper elevation={3} sx={{ p: 3, borderRadius: 2 }}>
                <Typography variant="h6" sx={{ color: 'text.secondary' }}>Total Income</Typography>
                <Typography variant="h4" sx={{ fontWeight: 'bold', color: '#2e7d32' }}>
                  {totalIncome.toLocaleString()} SP
                </Typography>
              </Paper>
            </Grid>
            <Grid item xs={12} md={4}>
              <Paper elevation={3} sx={{ p: 3, borderRadius: 2 }}>
                <Typography variant="h6" sx={{ color: 'text.secondary' }}>Total Expenses</Typography>
                <Typography variant="h4" sx={{ fontWeight: 'bold', color: '#d32f2f' }}>
                  {totalExpenses.toLocaleString()} SP
                </Typography>
              </Paper>
            </Grid>
            <Grid item xs={12} md={4}>
              <Paper elevation={3} sx={{ p: 3, borderRadius: 2 }}>
                <Typography variant="h6" sx={{ color: 'text.secondary' }}>Net Balance</Typography>
                <Typography variant="h4" sx={{ fontWeight: 'bold', color: totalBalance >= 0 ? '#2e7d32' : '#d32f2f' }}>
                  {totalBalance.toLocaleString()} SP
                </Typography>
              </Paper>
            </Grid>
          </Grid>
          
          {/* Control Buttons */}
          <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 3 }}>
            <Button variant="contained" color="primary" sx={{ fontWeight: 'bold' }}>
              Generate Financial Report
            </Button>
            <Box>
              <Button variant="outlined" sx={{ mx: 1, fontWeight: 'bold' }}>Active Campaigns</Button>
              <Button variant="outlined" sx={{ mx: 1, fontWeight: 'bold' }}>Completed Campaigns</Button>
              <Button variant="outlined" sx={{ fontWeight: 'bold' }}>View All</Button>
            </Box>
          </Box>
          
          {/* Campaigns List */}
          <Grid container spacing={3}>
            {campaigns.map((campaign) => (
              <Grid item xs={12} md={6} lg={4} key={campaign.id}>
                <CampaignCard campaign={campaign} />
              </Grid>
            ))}
          </Grid>
        </Box>
      </div>
    </div>
  );
};

export default FundsCampaigns;