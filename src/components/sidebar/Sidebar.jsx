import React from 'react'
import './sidebar.scss'
import { useNavigate } from 'react-router-dom';
import DashboardIcon from '@mui/icons-material/Dashboard';
import MenuIcon from '@mui/icons-material/Menu';
import AddBoxIcon from '@mui/icons-material/AddBox';
import SettingsIcon from '@mui/icons-material/Settings';
import MarkEmailUnreadIcon from '@mui/icons-material/MarkEmailUnread';
//import DarkModeIcon from '@mui/icons-material/DarkMode';
import DateRangeIcon from '@mui/icons-material/DateRange';
import AccessibleIcon from '@mui/icons-material/Accessible';
import ArchiveIcon from '@mui/icons-material/Archive';
import Diversity1Icon from '@mui/icons-material/Diversity1';
import InventoryIcon from '@mui/icons-material/Inventory';
import HealthAndSafetyIcon from '@mui/icons-material/HealthAndSafety';
import PersonIcon from '@mui/icons-material/Person';
//import VolunteerActivismIcon from '@mui/icons-material/VolunteerActivism';
import AddModeratorIcon from '@mui/icons-material/AddModerator';
import PermIdentityIcon from '@mui/icons-material/PermIdentity';
import WalletIcon from '@mui/icons-material/Wallet';




const Sidebar = () => {
  const navigate = useNavigate();

  // دالة للتعامل مع النقر على العناصر
  const handleNavigation = (path) => {
    navigate(path);
  };
  return (
    <div className='sidebar'>
        <div className='top'>
          <div className="logo">Kun Aonan</div>
        </div>
      <hr/>
        <div className='bottom'>
          <ul>
            <p className="title">
              MAIN
            </p>
            <li onClick={() => handleNavigation('/home')}>
                <DashboardIcon className='icon'/>
                <span>
                  DashBoard 
                </span>
            </li>
            <p className="title">
              CAMPAIGNS
            </p>
            <li>
                <MenuIcon className='icon'/>
                <span>
                 Show Campaigns
                </span>
            </li>
             <li>
                <AddBoxIcon className='icon'/>
                <span>
                 Add Campaign
                </span>
             </li>
             <li>
                <ArchiveIcon className='icon'/>
                <span>
                 Archive
                </span>
             </li>
             <p className="title">
              Donation Types Show
            </p>
            <li>
                <MenuIcon className='icon'/>
                <span>
                  Expiations 
                </span>
            </li>
            <li>
                <MenuIcon className='icon'/>
                <span>
                  General Donations 
                </span>
            </li>
            <li>
                <MenuIcon className='icon'/>
                <span>
                  Monetary donations
                </span>
            </li>
            <li>
                <MenuIcon className='icon'/>
                <span>
                  Zakat
                </span>
            </li>
            <li>
                <MenuIcon className='icon'/>
                <span>
                  Charitable donations
                </span>
            </li>
            <p className="title">
              Donation Types 
            </p>
            <li>
                <PermIdentityIcon className='icon'/>
                <span>
                  Expiations 
                </span>
            </li>
            <li>
                <PermIdentityIcon className='icon'/>
                <span>
                  General Donations 
                </span>
            </li>
            <li>
                <PermIdentityIcon className='icon'/>
                <span>
                  Monetary donations
                </span>
            </li>
            <li>
                <PermIdentityIcon className='icon'/>
                <span>
                  Zakat
                </span>
            </li>
            <li>
                <PermIdentityIcon className='icon'/>
                <span>
                  Charitable donations
                </span>
            </li>
            <p className="title">
              Guarantees
            </p>
            <li>
                <MenuIcon className='icon'/>
                <span>
                  Show Guarantees
                </span>
            </li>
            <li>
                <AddBoxIcon className='icon'/>
                <span>
                 Add Guarantee
                </span>
             </li>
             <li>
                <ArchiveIcon className='icon'/>
                <span>
                 Archive
                </span>
             </li>
            <p className="title">
              VOLUNTEERS
            </p>
            
            <li>
                <MenuIcon className='icon'/>
                <span>
                  Show Volunteers
                </span>
            </li>
            <li>
                <MarkEmailUnreadIcon className='icon'/>
                <span>
                  Volunteer Requests
                </span>
            </li>
            <p className="title">
              BENEFICIARIES
            </p>
            <li>
                <MenuIcon className='icon'/>
                <span>
                  Show Beneficiaries
                </span>
            </li>
            <li>
                <MarkEmailUnreadIcon className='icon'/>
                <span>
                  Benefit requests
                </span>
            </li>
            <p className="title">
              Expiations
            </p>
            <li>
                <MenuIcon className='icon'/>
                <span>
                  Show Expiations 
                </span>
            </li>
            <p className="title">
              FUNDS
            </p>
            <li>
                <DateRangeIcon className='icon'/>
                <span>
                  Campaigns
                </span>
            </li>
            <li>
                <AccessibleIcon className='icon'/>
                <span>
                  Humanitarian cases
                </span>
            </li>
            <li>
                <Diversity1Icon className='icon'/>
                <span>
                  Guarantees
                </span>
            </li>
            <li>
                <InventoryIcon className='icon'/>
                <span>
                  General Donations
                </span>
            </li>
            <li>
                <InventoryIcon className='icon'/>
                <span>
              Expiations   
                </span>
            </li>
            <li>
                <HealthAndSafetyIcon className='icon'/>
                <span>
              Health  
                </span>
            </li>
            <li>
                <PersonIcon className='icon'/>
                <span>
              Orphans  
                </span>
            </li>
            <li>
                <AddModeratorIcon className='icon'/>
                <span>
              Support team   
                </span>
            </li>
            
             <p className="title">
              USERS
            </p>
            <li>
                <PersonIcon className='icon'/>
                <span>
                  Donators
                </span>
            </li>
            <li>
                <PersonIcon className='icon'/>
                <span>
                  Volunteers
                </span>
            </li>
            <li>
                <PersonIcon className='icon'/>
                <span>
                  Beneficiaries
                </span>
            </li>
            <p className="title">
              Card recharge
            </p>
            <li>
                <WalletIcon className='icon'/>
                <span>
                   Charge User's Card
                </span>
            </li>
            <p className="title">
              Messages
            </p>
            <li>
                <MarkEmailUnreadIcon className='icon'/>
                <span>
                  Emails
                </span>
            </li>
            <p className="title">
              Reports
            </p>
            <li>
                <MenuIcon className='icon'/>
                <span>
                  Create report
                </span>
            </li>
            <p className="title">
              SETTINGS
            </p>
            <li>
                <SettingsIcon className='icon'/>
                <span>
                  Settings
                </span>
            </li>
            
           
          </ul>
        </div>

    </div>
  )
}

export default Sidebar 