library(vegan)
library(ggplot2)
library(ellipse)
library(ggrepel)
MyMeta = data.frame(
  sites = c(1,2,3,4),
  type=c("soilsource1", "soilsource2", "soilsource3", "soilsource4"),
  row.names = "sites")
nmds<-read.table(file="final.tx.thetayc.2.lt.ave.nmds.axes", header=T)
NMDS = data.frame(MDS1 = nmds$axis1, MDS2 = nmds$axis2, group=MyMeta$type)
group=MyMeta$type

tiff('Fig4_NMDS.tiff',width=8, height=6, units="in", res=300, compression = "lzw")
drawout=ggplot(data = NMDS, aes(MDS1, MDS2)) + geom_point(aes(color = group),size=2.5, alpha=0.5)+
  geom_hline(yintercept=0, linetype="dashed", size=.2) + geom_vline(xintercept=0, linetype="dashed", size=.2)+
  geom_point(aes(color = group),size=2.5, alpha=0.5)+
  scale_color_manual(labels = c("Soil1", "Soil2","Soil3","Soil4"), values=c("red","green3","orange","blue"))+
  guides(color=guide_legend("Source"))+
  geom_text_repel(aes(label = nmds$group), box.padding = unit(0.35, "lines"), segment.color=NA, size=3.5)+
  theme_bw()+
  theme(legend.position="right", legend.title=element_text(colour="black", size=10, face="bold"), legend.text = element_text(colour="black", size = 8, face = "plain"))
drawout
dev.off()

# Metadata =>
# points<-read.table("soildetailex1Thetayc.pearson.corr.axes3", header=T)
# x1<-points$axis1
# y1<-points$axis2
# labels<-points$Feature #point$Feature
# plot=geom_segment(data=points, aes(x=0, y=0, xend=x1, yend=y1), arrow=arrow(length=unit(0.2,"cm"),type="closed", angle=40), alpha=0.75, color="gray")
# 
# textAnno <- annotate("text", x=0.8, y=-0.8, label="Temperature", size=5, fontface="bold.italic", colour = "red")
# drawout+plot+textAnno+annotate("text", x=-0.65, y=0.68, label="pH", size=5, fontface="bold.italic", colour = "red") 
# 
# # taxonomy =>
# points<-read.table("genus2contaxonomysoil16SmergeDupex3.txt", header=T)
# x1<-points$axis1
# y1<-points$axis2
# labels<-points$taxon #point$OTU #taxonomy
# plot=geom_segment(data=points, aes(x=0, y=0, xend=x1, yend=y1), arrow=arrow(length=unit(0.2,"cm"),type="closed", angle=40), alpha=0.75, color="gray")
# 
# drawout+plot+geom_text_repel(data=points, aes(x=x1, y=y1, label =labels), box.padding = unit(0.01, "cm"), force = 2, segment.color=NA, size=3, color="red") # segment.color ="black" , segment.size = 0.1
