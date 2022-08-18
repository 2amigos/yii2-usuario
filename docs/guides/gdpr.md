# GDPR and Yii2-usuario

EU regulation
The General Data Protection Regulation (GDPR) (EU) 2016/679 is a regulation in EU law on data protection and privacy for all individuals within the European Union and the European Economic Area. It also addresses the export of personal data outside the EU and EEA. The GDPR aims primarily to give control to citizens and residents over their personal data and to simplify the regulatory environment for international business by unifying the regulation within the EU

## Enable GDPR

To enable support in yii2-usuario set `enableGdprCompliance` to `true` and set
  `gdprPrivacyPolicyUrl` with an url pointing to your privacy policy.

### At this moment a few measures apply to your app:

#### Data processing consent:
GDPR says: [Article 7](https://gdpr.algolia.com/gdpr-article-7)

> Where processing is based on consent, the controller shall be able to demonstrate that the data subject has consented to processing of his or her personal data.\[...]

All users must give consent of data processing to register.
Also consent will be stored in db with the user data.

If you have users before GDPR law you can force them to give consent via GDPRrequireConsentToAll. You must use also in your accessControl behaviors the yii2-usuario accessRuleFilter. Any registerd user that has not give consent will be redirected in any action to the consent screen except those defined in `GDPRconsentExcludedUrls`

#### Data portability

GDPR says: [Article 20](https://gdpr.algolia.com/gdpr-article-20)
> The data subject shall have the right to receive the personal data concerning him or her, which he
> or she has provided to a controller, in a structured, commonly used and machine-readable format\[...]

Users now have a privacy page in their account settings where they can export his/her personal data
in a csv file.
If you collect additional personal information you can to export by adding to
`gdprExportProperties`.
> Export use `ArrayHelper::getValue()` to extract information, so you can use links to relations.


#### Right to be forgotten

GDPR says: [Article 17](https://gdpr.algolia.com/gdpr-article-17)
> The data subject shall have the right to obtain from the controller the erasure of personal data concerning him or her without undue delay and the controller shall have the obligation to erase personal data without undue delay\[...]

In privacy page, users will find a button to delete their personal information.
The behavior differs depending module configuration.

If  `$allowAccountDelete` is set to `true` the account will be fully deleted when clicking *Delete* button,
while when if that setting is set to `false` the module will remove social network connections and
replace the personal data with a custom alias defined in `$gdprAnonymizePrefix`.

The account will be blocked and marked as `gdpr_deleted`.

That way you can keep your site operation as normal.

> If you need to delete additional information use the `GdprEvent::EVENT_BEFORE_DELETE`.
