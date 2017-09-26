args<-commandArgs(TRUE)
library(reshape2)
library(ggplot2)
library(scales)
data=read.table(args[1], sep = "\t", header=T)
data1=as.data.frame(t(data[,2:ncol(data)]))
colnames(data1)=data$taxonomy
data2=cbind(taxonomy=row.names(data1),data1)
data3=melt(data2,id.var="taxonomy")
j=nrow(data)

gg<-ggplot(data3,aes(x=taxonomy,y=value, fill=variable))
gg1=gg+geom_bar(stat="identity", colour='black', size=0)+scale_fill_manual(name="",values = sample(colors(),j))+guides(fill=guide_legend(override.aes=list(colour=NA),ncol=5))+xlab("")+ylab("%relative abundance of bacterial phyla")+theme(axis.text.x=element_text(angle=45,hjust=1, size=12)) #No line border
gg2=gg1+theme(legend.direction="horizontal", legend.position="bottom", legend.box="vertical", legend.title.align=0) #+ scale_fill_discrete(name="")
#gg2
ggsave(args[2], plot=gg2, width=20, height=25, units="cm",dpi=300)
