import { createBrowserRouter , RouterProvider } from "react-router-dom";
import  Home  from "./pages/home/Home";
import Campaign from "./pages/campaign/Campaign"
import New from "./pages/new/New"
import Volunteer from "./pages/volunteer/Volunteer"
import Single from "./pages/single/Single"
import Donators from "./pages/donators/Donators"
import CampaignAdd from "./pages/campaign_add/CampaignAdd"
import CampaignArchive from "./pages/campaign_archive/CampaignArchive";
import Campaign_details from "./pages/campaign_details/Campaign_details"
import Volunteer_details from "./pages/volunteer_details/Volunteer_details";
import VolunteerRequest from "./pages/volunteer_request/Volunteer_request";
import FundsCampaigns from "./pages/fundsCampaigns/FundsCampaigns";

const router = createBrowserRouter([

{
  path: "/",
  element: <Home />
},
{
  path: "/campaign/",
  element: <Campaign />
},
{
  path: "/donators/",
  element: <Donators />
},
{
  path: "/volunteer",
  element: <Volunteer />
},
{
  path: "/campaignAdd",
  element: <CampaignAdd />
},
{
  path: "/campaignArchive",
  element: <CampaignArchive />
},
{
  path: "/volunteer_details",
  element: <Volunteer_details />
},
{
  path: "/volunteer_request",
  element: <VolunteerRequest />
},
{
  path: "/fundsCampaigns",
  element: <FundsCampaigns />
},
{
  path: "/campaign/:campaignId/new",
  element: <New />
},
{
  path: "/volunteer/:volunteerId/new",
  element: <New />
},
{
  path: "/fundsCampaigns/:fundsCampaignsId/new",
  element: <New />
},
{
  path: "/volunteer_details/:volunteer_detailsId",
  element: <New />
},
{
  path: "/volunteer_request/:volunteer_requestId",
  element: <New />
},
{
  path: "/fundsCampaigns/:fundsCampaignsId",
  element: <New />
},
{
  path: "/campaign/:campaignId",
  element: <Single />
},
{
  path: "/donators/:donatorsId",
  element: <New />
},
{
  path: "/campaignAdd/:campaignAddId/new",
  element: <New />
},
{
  path: "/campaignArchive/:campaignArchiveId",
  element: <New />
},
{
  path: "/volunteer/:volunteerId",
  element: <New />
},
{
  path: "/campaign_details",
  element: <Campaign_details />
},
{
  path: "/campaign_details/:campaign_detailsId",
  element: <New />
},
{
  path: "/fundsCampaigns/:fundsCampaignsId",
  element: <New />
}
])
function App() {
  return (
    <div className="app">
      <RouterProvider router = {router}/>
    </div>
  );
}

export default App;
