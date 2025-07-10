import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/HumanitarianCases_view_page.dart';
import 'package:charity_project/view/Sponsorships_view_page.dart';
import 'package:charity_project/view/app_text_style.dart';
import 'package:charity_project/view/background.dart';
import 'package:charity_project/view/campaign_view_page.dart';
import 'package:charity_project/view/one_campaign_page.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:percent_indicator/flutter_percent_indicator.dart';
 double raisedamount =1000;
 double goalamount = 1200;
    final progress = (raisedamount / goalamount).clamp(0.0, 1.0);
class DonaitionCategoryViewPage extends StatefulWidget {
  const DonaitionCategoryViewPage({super.key,required this.Category});
final String Category;
  @override
  State<DonaitionCategoryViewPage> createState() => _DonaitionCategoryViewPageState();
}

class _DonaitionCategoryViewPageState extends State<DonaitionCategoryViewPage> {

 
  @override
Widget build(BuildContext context) {
  if (widget.Category == "Campaigns") {
    return CampaignViewPage();  }
    else if(widget.Category == "HumanitarianCases"){
      return HumanitariancasesViewPage();
    }
    else if(widget.Category == "Sponsorships"){
      return SponsorshipsViewPage();
    }
  return Scaffold(
    
    body: 
        
         Center(child: Text("لا يوجد بيانات")), // أو أي واجهة أخرى
  );
}
}